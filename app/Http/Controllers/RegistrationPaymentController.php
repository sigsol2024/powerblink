<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Services\PaystackService;
use App\Services\RegistrationPaymentCompletionService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationPaymentController extends Controller
{
    public function show(Request $request, RegistrationService $registrations): View
    {
        /** @var Registration $registration */
        $registration = $request->attributes->get('registration');
        $registration->load(['guardian', 'program']);

        $feeKobo = $registrations->computeRegistrationFee($registration);

        $pendingPayment = $this->findReusablePendingPayment($registration);

        return view('registrations.pay', [
            'title' => __('Registration payment'),
            'registration' => $registration,
            'feeKobo' => $feeKobo,
            'feeDisplay' => format_currency($feeKobo / 100),
            'token' => $registration->payment_token,
            'pendingPayment' => $pendingPayment,
        ]);
    }

    public function initialize(
        Request $request,
        RegistrationService $registrations,
        PaystackService $paystack,
    ): RedirectResponse {
        /** @var Registration $registration */
        $registration = $request->attributes->get('registration');
        $registration->load(['guardian', 'program']);

        $email = (string) ($registration->guardian?->email ?? '');
        if ($email === '') {
            return back()->withErrors(['payment' => __('Guardian email is required for payment.')]);
        }

        if ($registration->status === 'activated') {
            return back()->with('status', __('This registration is already activated.'));
        }

        if ($registration->status !== 'awaiting_payment') {
            return back()->withErrors(['payment' => __('This registration is not awaiting payment.')]);
        }

        $existingSuccess = RegistrationPayment::query()
            ->where('registration_id', $registration->id)
            ->where('status', 'success')
            ->exists();

        if ($existingSuccess) {
            return back()->with('status', __('Payment already recorded for this registration.'));
        }

        $reusable = $this->findReusablePendingPayment($registration);
        if ($reusable) {
            $authorizationUrl = data_get($reusable->gateway_payload, 'data.authorization_url')
                ?? data_get($reusable->gateway_payload, 'authorization_url');

            if (is_string($authorizationUrl) && $authorizationUrl !== '') {
                return redirect()->away($authorizationUrl);
            }
        }

        $amount = $registrations->computeRegistrationFee($registration);

        try {
            $payment = DB::transaction(function () use ($registration, $amount): RegistrationPayment {
                $locked = Registration::query()->whereKey($registration->id)->lockForUpdate()->first();
                if (! $locked || $locked->status !== 'awaiting_payment') {
                    throw new \RuntimeException('registration_not_payable');
                }

                $reference = 'REG_'.Str::upper(Str::random(16));

                return RegistrationPayment::query()->create([
                    'registration_id' => $registration->id,
                    'season_id' => $registration->season_id,
                    'type' => 'registration_fee',
                    'provider' => 'paystack',
                    'reference' => $reference,
                    'status' => 'pending',
                    'amount' => $amount,
                    'currency' => 'NGN',
                ]);
            });
        } catch (\RuntimeException) {
            return back()->withErrors(['payment' => __('This registration is not awaiting payment.')]);
        }

        try {
            $init = $paystack->initializeTransaction(
                email: $email,
                amountKobo: $amount,
                currency: 'NGN',
                reference: $payment->reference,
                callbackUrl: route('registration.pay.callback', absolute: true),
                metadata: [
                    'registration_id' => $registration->id,
                    'type' => 'registration_fee',
                ],
            );
        } catch (\Throwable) {
            $payment->update(['status' => 'failed']);

            return back()->withErrors(['payment' => __('Unable to start payment. Please try again.')]);
        }

        $payment->update([
            'gateway_payload' => $init['raw'] ?? null,
        ]);

        return redirect()->away((string) ($init['authorization_url'] ?? route('registration.pay.show', $registration->payment_token)));
    }

    public function callback(
        Request $request,
        PaystackService $paystack,
        RegistrationPaymentCompletionService $completion,
    ): RedirectResponse {
        $reference = trim((string) $request->query('reference', ''));
        if ($reference === '') {
            return redirect()->route('home')->withErrors(['payment' => __('Payment reference missing.')]);
        }

        $payment = RegistrationPayment::query()
            ->where('reference', $reference)
            ->with('registration')
            ->first();

        if (! $payment || ! $payment->registration) {
            return redirect()->route('home')->withErrors(['payment' => __('Payment not found.')]);
        }

        if ($payment->status === 'success' || $payment->registration->status === 'activated') {
            return redirect()->route('home')->with('status', __('Payment already processed. Welcome to PowerBlink FC!'));
        }

        try {
            $verified = $paystack->verify($reference);
        } catch (\Throwable) {
            return redirect()->route('home')->withErrors(['payment' => __('Unable to verify payment. Please contact support.')]);
        }

        if ($completion->complete($payment, $verified)) {
            return redirect()->route('home')->with('status', __('Payment successful! Check your email for activation details.'));
        }

        return redirect()->route('home')->withErrors(['payment' => __('Payment was not successful.')]);
    }

    private function findReusablePendingPayment(Registration $registration): ?RegistrationPayment
    {
        $minutes = max(5, (int) config('powerblink.payment_pending_reuse_minutes', 30));
        $cutoff = now()->subMinutes($minutes);

        return RegistrationPayment::query()
            ->where('registration_id', $registration->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', $cutoff)
            ->latest('id')
            ->first();
    }
}
