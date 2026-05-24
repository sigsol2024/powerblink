<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\VehicleVariant;
use App\Services\OrderPaymentCompletionService;
use App\Services\PaystackService;
use App\Support\Cart;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Cart::itemCount() === 0) {
            return redirect()->route('cart.index')->with('status', __('Your bag is empty.'));
        }

        return view('pages.checkout.index', [
            'title' => __('Checkout'),
            'lines' => Cart::lines(),
            'subtotal' => Cart::subtotal(),
            'subtotalKobo' => Money::nairaToKobo(Cart::subtotal()),
        ]);
    }

    public function store(Request $request, PaystackService $paystack): RedirectResponse
    {
        if (Cart::itemCount() === 0) {
            return redirect()->route('cart.index')->with('status', __('Your bag is empty.'));
        }

        $data = $request->validate([
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

        $order = DB::transaction(function () use ($data, $subtotalKobo, $shippingKobo, $taxKobo, $totalKobo) {
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

        try {
            $init = $paystack->initialize(
                $order,
                $data['customer_email'],
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

    public function show(Request $request, Order $order): View
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($order->isPaid(), 404);

        $order->load(['items', 'payment']);

        return view('pages.orders.show', [
            'title' => __('Order :number', ['number' => $order->order_number]),
            'order' => $order,
        ]);
    }

    private function signedConfirmedUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.confirmed',
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
