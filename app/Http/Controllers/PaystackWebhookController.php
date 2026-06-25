<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaystackWebhookPayment;
use App\Models\RegistrationPayment;
use App\Services\RegistrationPaymentCompletionService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaystackService $paystack,
        RegistrationPaymentCompletionService $completion,
    ): Response {
        $secret = (string) config('services.paystack.webhook_secret');
        if ($secret === '') {
            Log::warning('Paystack webhook received but PAYSTACK_WEBHOOK_SECRET is not set.');

            return response('Webhook not configured', 503);
        }

        $signature = (string) $request->header('x-paystack-signature', '');
        $payload = $request->getContent();
        $expected = hash_hmac('sha512', $payload, $secret);

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            return response('Invalid signature', 401);
        }

        $data = $request->json()->all();
        $event = (string) ($data['event'] ?? '');

        if ($event !== 'charge.success') {
            return response('Ignored', 200);
        }

        $reference = trim((string) data_get($data, 'data.reference', ''));
        if ($reference === '') {
            return response('Missing reference', 422);
        }

        $payment = RegistrationPayment::query()
            ->where('reference', $reference)
            ->with('registration')
            ->first();

        if (! $payment || ! $payment->registration) {
            return response('Payment not found', 404);
        }

        if ($payment->status === 'success' || $payment->registration->status === 'activated') {
            $completion->fulfillPaidRegistration($payment->registration);

            return response('Already processed', 200);
        }

        try {
            $verified = $paystack->verify($reference);
        } catch (\Throwable $e) {
            Log::error('Paystack webhook verify failed; queueing retry', [
                'reference' => $reference,
                'message' => $e->getMessage(),
            ]);
            ProcessPaystackWebhookPayment::dispatch($payment->id, $reference);

            return response('Queued for retry', 200);
        }

        if ($completion->complete($payment, $verified)) {
            return response('OK', 200);
        }

        return response('Payment not successful', 422);
    }
}
