<?php

namespace App\Http\Middleware;

use App\Models\Registration;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRegistrationPaymentToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = trim((string) $request->route('token', ''));
        if ($token === '') {
            return $this->invalidResponse();
        }

        $registration = Registration::query()
            ->where('payment_token', $token)
            ->first();

        if (! $registration) {
            return $this->invalidResponse();
        }

        if ($registration->status !== 'awaiting_payment') {
            return $this->invalidResponse();
        }

        if ($registration->payment_token_used_at !== null) {
            return $this->invalidResponse();
        }

        if ($registration->payment_token_expires_at !== null && $registration->payment_token_expires_at->isPast()) {
            return $this->invalidResponse();
        }

        $request->attributes->set('registration', $registration);

        return $next($request);
    }

    private function invalidResponse(): Response
    {
        return response()->view('registrations.payment-invalid', [
            'title' => __('Payment link unavailable'),
        ], 403);
    }
}
