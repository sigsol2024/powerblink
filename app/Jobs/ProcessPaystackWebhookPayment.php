<?php

namespace App\Jobs;

use App\Models\RegistrationPayment;
use App\Services\PaystackService;
use App\Services\RegistrationPaymentCompletionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPaystackWebhookPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $paymentId,
        public readonly string $reference,
    ) {
    }

    public function handle(PaystackService $paystack, RegistrationPaymentCompletionService $completion): void
    {
        $payment = RegistrationPayment::query()->with('registration')->find($this->paymentId);
        if (! $payment || ! $payment->registration) {
            return;
        }

        if ($payment->status === 'success' || $payment->registration->status === 'activated') {
            $completion->fulfillPaidRegistration($payment->registration);

            return;
        }

        try {
            $verified = $paystack->verify($this->reference);
        } catch (\Throwable $e) {
            Log::error('Queued Paystack verify failed', [
                'reference' => $this->reference,
                'message' => $e->getMessage(),
            ]);

            return;
        }

        $completion->complete($payment, $verified);
    }
}
