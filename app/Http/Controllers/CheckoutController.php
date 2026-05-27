<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Services\Mail\OutboundMailService;
use App\Services\OrderPaymentCompletionService;
use App\Services\PaystackService;
use App\Support\Cart;
use App\Support\CheckoutPaymentMethods;
use App\Support\Money;
use App\Support\SiteSettingDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Cart::itemCount() === 0) {
            return redirect()->route('cart.index')->with('status', __('Your bag is empty.'));
        }

        $paymentMethods = CheckoutPaymentMethods::enabledForCheckout();

        return view('pages.checkout.index', [
            'paymentMethodsEnabled' => $paymentMethods !== [],
            'title' => __('Checkout'),
            'lines' => Cart::lines(),
            'subtotal' => Cart::subtotal(),
            'subtotalKobo' => Money::nairaToKobo(Cart::subtotal()),
            'paymentMethods' => $paymentMethods,
            'defaultPaymentMethod' => old('payment_method', $paymentMethods[0]['id'] ?? 'paystack'),
        ]);
    }

    public function store(Request $request, PaystackService $paystack, OutboundMailService $mailer): RedirectResponse
    {
        if (Cart::itemCount() === 0) {
            return redirect()->route('cart.index')->with('status', __('Your bag is empty.'));
        }

        $enabledIds = array_column(CheckoutPaymentMethods::enabledForCheckout(), 'id');
        if ($enabledIds === []) {
            return back()->withErrors([
                'checkout' => __('No payment methods are enabled. Please contact the store.'),
            ])->withInput();
        }

        $data = $request->validate([
            'payment_method' => ['required', 'string', Rule::in($enabledIds)],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_state' => ['nullable', 'string', 'max:120'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'size:2'],
        ]);

        $subtotalNaira = Cart::subtotal();
        $subtotalKobo = Money::nairaToKobo($subtotalNaira);
        $shippingKobo = 0;
        $taxKobo = 0;
        $totalKobo = $subtotalKobo + $shippingKobo + $taxKobo;

        if ($totalKobo < 100) {
            return back()->withErrors(['checkout' => __('Order total must be at least ₦1.')])->withInput();
        }

        $order = $this->createOrder($data, $subtotalKobo, $shippingKobo, $taxKobo, $totalKobo);
        $paymentMethod = (string) $data['payment_method'];

        if ($paymentMethod === 'paystack') {
            if (! CheckoutPaymentMethods::isPaystackConfigured()) {
                return back()
                    ->withErrors(['checkout' => __('Paystack is enabled but API keys are missing. Add PAYSTACK_SECRET_KEY to .env.')])
                    ->withInput();
            }

            return $this->redirectToPaystack($order, $data['customer_email'], $paystack);
        }

        $reference = match ($paymentMethod) {
            'bank_transfer' => 'BT-'.$order->order_number,
            'pay_on_delivery' => 'COD-'.$order->order_number,
            default => 'MAN-'.$order->order_number,
        };

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => $paymentMethod,
            'reference' => $reference,
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => $order->currency,
            'gateway_payload' => null,
        ]);

        $this->notifyAdminNewPendingOrder($order, $mailer);

        Cart::clear();
        session()->forget('checkout.pending_order_id');
        session(['order.awaiting_ack' => $order->id]);

        return redirect()->to($this->signedShowUrl($order));
    }

    public function paystackCallback(
        Request $request,
        PaystackService $paystack,
        OrderPaymentCompletionService $completion,
    ): RedirectResponse {
        $reference = trim((string) $request->query('reference', ''));
        if ($reference === '') {
            return redirect()->route('cart.index')->withErrors(['payment' => __('Missing payment reference.')]);
        }

        $payment = Payment::query()->where('reference', $reference)->with('order')->first();
        if (! $payment || ! $payment->order) {
            return redirect()->route('cart.index')->withErrors(['payment' => __('Payment not found.')]);
        }

        $order = $payment->order;

        if ($order->isPaid()) {
            Cart::clear();
            session()->forget('checkout.pending_order_id');
            $completion->fulfillPaidOrder($order);

            return redirect()->to($this->signedConfirmedUrl($order));
        }

        try {
            $payload = $paystack->verify($reference);
        } catch (\Throwable $e) {
            return redirect()->route('checkout.index')->withErrors(['payment' => __('Could not verify payment.')]);
        }

        if ($completion->complete($payment, $payload)) {
            Cart::clear();
            session()->forget('checkout.pending_order_id');

            return redirect()->to($this->signedConfirmedUrl($order->fresh()));
        }

        $payment->update(['status' => 'failed']);
        $order->update(['status' => 'failed']);

        return redirect()->route('checkout.index')->withErrors(['payment' => __('Payment was not successful.')]);
    }

    public function confirmed(Request $request, Order $order): View
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($order->isPaid(), 404);

        $order->load('items');

        return view('pages.orders.confirmed', [
            'title' => __('Order confirmed'),
            'order' => $order,
            'signedShowUrl' => $this->signedShowUrl($order),
        ]);
    }

    public function placed(Request $request, Order $order): View
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($order->status === 'pending_payment', 404);

        $order->load(['items', 'payment']);
        session()->forget('order.awaiting_ack');

        return view('pages.orders.placed', [
            'title' => __('Order received'),
            'order' => $order,
            'signedShowUrl' => $this->signedShowUrl($order),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($order->isPaid() || $order->status === 'pending_payment', 404);

        $order->load(['items', 'payment']);
        $site = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());
        $provider = (string) ($order->payment?->provider ?? '');
        $showPaymentInstructions = $order->status === 'pending_payment'
            && in_array($provider, ['bank_transfer', 'pay_on_delivery'], true);

        return view('pages.orders.show', [
            'title' => __('Order :number', ['number' => $order->order_number]),
            'order' => $order,
            'showPaymentInstructions' => $showPaymentInstructions,
            'paymentProvider' => $provider,
            'bankTransferDetails' => $provider === 'bank_transfer'
                ? CheckoutPaymentMethods::bankTransferDetails($site)
                : '',
            'payOnDeliveryNote' => $provider === 'pay_on_delivery'
                ? CheckoutPaymentMethods::payOnDeliveryNote($site)
                : '',
            'signedPlacedUrl' => $this->signedPlacedUrl($order),
            'awaitingAck' => (int) session('order.awaiting_ack') === $order->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createOrder(array $data, int $subtotalKobo, int $shippingKobo, int $taxKobo, int $totalKobo): Order
    {
        return DB::transaction(function () use ($data, $subtotalKobo, $shippingKobo, $taxKobo, $totalKobo) {
            $order = Order::query()->create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'shipping_address_line1' => $data['shipping_address_line1'],
                'shipping_address_line2' => $data['shipping_address_line2'] ?? null,
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'] ?? null,
                'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                'shipping_country' => strtoupper($data['shipping_country']),
                'subtotal' => $subtotalKobo,
                'shipping' => $shippingKobo,
                'tax' => $taxKobo,
                'total' => $totalKobo,
                'currency' => 'NGN',
                'status' => 'pending_payment',
            ]);

            foreach (Cart::lines() as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'vehicle_id' => (int) $line['vehicle_id'],
                    'vehicle_variant_id' => ! empty($line['vehicle_variant_id']) ? (int) $line['vehicle_variant_id'] : null,
                    'sku' => $line['sku'] ?? null,
                    'name' => (string) $line['name'],
                    'unit_price' => Money::nairaToKobo((int) $line['unit_price']),
                    'qty' => (int) $line['qty'],
                    'line_total' => Money::nairaToKobo((int) $line['unit_price']) * (int) $line['qty'],
                ]);
            }

            return $order;
        });
    }

    private function redirectToPaystack(Order $order, string $email, PaystackService $paystack): RedirectResponse
    {
        try {
            $init = $paystack->initialize(
                $order,
                $email,
                route('payment.paystack.callback', [], true)
            );
        } catch (\Throwable $e) {
            $order->update(['status' => 'failed']);

            return back()
                ->withErrors(['checkout' => __('Unable to start payment. Please try again.')])
                ->withInput();
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'paystack',
            'reference' => $init['reference'],
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => $order->currency,
            'gateway_payload' => $init['raw'] ?? null,
        ]);

        session(['checkout.pending_order_id' => $order->id]);

        return redirect()->away($init['authorization_url']);
    }

    private function notifyAdminNewPendingOrder(Order $order, OutboundMailService $mailer): void
    {
        $order->loadMissing('items', 'payment');
        $to = SiteSettingDefaults::resolvedNotifyEmail();
        $toName = SiteSettingDefaults::resolvedNotifyRecipientName();
        $subject = __('New order awaiting payment — :number', ['number' => $order->order_number]);
        $html = '<p>'.e(__('A new order was placed and is awaiting payment confirmation.')).'</p>'
            .'<p><strong>'.e(__('Order')).':</strong> '.e($order->order_number).'</p>'
            .'<p><strong>'.e(__('Payment method')).':</strong> '.e((string) ($order->payment?->provider ?? '')).'</p>'
            .'<p><a href="'.e(route('admin.orders.show', $order)).'">'.e(__('View in admin')).'</a></p>';

        try {
            $mailer->send($to, $toName, $subject, $html, $order->customer_email, $order->customer_name);
        } catch (Throwable $e) {
            Log::warning('Pending order admin email failed', ['order_id' => $order->id, 'message' => $e->getMessage()]);
        }
    }

    private function signedConfirmedUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.confirmed',
            now()->addDays(90),
            ['order' => $order->id]
        );
    }

    private function signedPlacedUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.placed',
            now()->addDays(90),
            ['order' => $order->id]
        );
    }

    private function signedShowUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.show',
            now()->addDays(90),
            ['order' => $order->id]
        );
    }
}
