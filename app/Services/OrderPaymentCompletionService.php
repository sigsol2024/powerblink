<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\VehicleVariant;
use App\Services\Mail\OutboundMailService;
use App\Support\SiteSettingDefaults;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;

class OrderPaymentCompletionService
{
    public function __construct(
        private readonly OutboundMailService $mailer,
    ) {
    }

    /**
     * Idempotently mark payment successful, deduct stock once, send emails once.
     *
     * @param  array<string, mixed>  $gatewayPayload
     */
    public function complete(Payment $payment, array $gatewayPayload): bool
    {
        $txData = is_array($gatewayPayload['data'] ?? null) ? $gatewayPayload['data'] : [];
        $txStatus = (string) ($txData['status'] ?? '');
        $amountPaid = (int) ($txData['amount'] ?? 0);
        $apiSuccess = (bool) ($gatewayPayload['status'] ?? false);

        if (! $apiSuccess || $txStatus !== 'success') {
            return false;
        }

        return (bool) DB::transaction(function () use ($payment, $gatewayPayload, $amountPaid) {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $payment) {
                return false;
            }

            $order = Order::query()->whereKey($payment->order_id)->lockForUpdate()->first();
            if (! $order) {
                return false;
            }

            if ($amountPaid > 0 && $amountPaid < (int) $order->total) {
                return false;
            }

            $payment->update([
                'gateway_payload' => $gatewayPayload,
            ]);

            $alreadyPaid = $order->isPaid();

            if (! $alreadyPaid) {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => $payment->paid_at ?? now(),
                ]);
                $order->update(['status' => 'paid']);
            } elseif ($payment->status !== 'success') {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => $payment->paid_at ?? now(),
                ]);
            }

            if ($order->stock_deducted_at === null) {
                $this->deductStock($order);
                $order->update(['stock_deducted_at' => now()]);
            }

            $this->sendCustomerConfirmationIfNeeded($order);
            $this->sendAdminNotificationIfNeeded($order);

            return true;
        });
    }

    /**
     * Run stock deduction and emails for an order already marked paid (e.g. missed webhook emails).
     */
    public function fulfillPaidOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if (! $order || ! $order->isPaid()) {
                return;
            }

            if ($order->stock_deducted_at === null) {
                $this->deductStock($order);
                $order->update(['stock_deducted_at' => now()]);
            }

            $this->sendCustomerConfirmationIfNeeded($order);
            $this->sendAdminNotificationIfNeeded($order);
        });
    }

    private function deductStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $qty = max(1, (int) $item->qty);

            if ($item->vehicle_variant_id) {
                $variant = VehicleVariant::query()
                    ->whereKey($item->vehicle_variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($variant) {
                    $variant->update(['stock' => max(0, (int) $variant->stock - $qty)]);
                }

                continue;
            }

            if ($item->vehicle_id) {
                $vehicle = Vehicle::query()
                    ->whereKey($item->vehicle_id)
                    ->lockForUpdate()
                    ->first();

                if ($vehicle && isset($vehicle->stock)) {
                    $vehicle->stock = max(0, (int) $vehicle->stock - $qty);
                    $vehicle->save();
                }
            }
        }
    }

    private function sendCustomerConfirmationIfNeeded(Order $order): void
    {
        if ($order->customer_notified_at !== null) {
            return;
        }

        $orderUrl = URL::temporarySignedRoute(
            'order.show',
            now()->addDays(90),
            ['order' => $order->id]
        );

        $subject = __('Order confirmed — :number', ['number' => $order->order_number]);
        $html = view('emails.order-confirmation-customer', [
            'order' => $order->loadMissing('items'),
            'orderUrl' => $orderUrl,
        ])->render();

        try {
            $this->mailer->send(
                $order->customer_email,
                $order->customer_name,
                $subject,
                $html
            );
            $order->update(['customer_notified_at' => now()]);
        } catch (Throwable $e) {
            Log::warning('Order customer confirmation email failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function sendAdminNotificationIfNeeded(Order $order): void
    {
        if ($order->admin_notified_at !== null) {
            return;
        }

        $adminUrl = route('admin.orders.show', $order);
        $subject = __('New paid order — :number', ['number' => $order->order_number]);
        $html = view('emails.order-confirmation-admin', [
            'order' => $order->loadMissing('items'),
            'adminUrl' => $adminUrl,
        ])->render();

        $to = SiteSettingDefaults::resolvedNotifyEmail();
        $toName = SiteSettingDefaults::resolvedNotifyRecipientName();

        try {
            $this->mailer->send($to, $toName, $subject, $html, $order->customer_email, $order->customer_name);
            $order->update(['admin_notified_at' => now()]);
        } catch (Throwable $e) {
            Log::warning('Order admin notification email failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
