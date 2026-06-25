<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    /**
     * @param  array<string, mixed>  $metadata
     * @return array{reference:string,authorization_url:string,access_code:string,raw:array<string,mixed>}
     */
    public function initializeTransaction(
        string $email,
        int $amountKobo,
        string $currency,
        ?string $reference = null,
        ?string $callbackUrl = null,
        array $metadata = [],
    ): array {
        $secret = (string) config('services.paystack.secret_key');
        if ($secret === '') {
            throw new \RuntimeException('Paystack secret key is not configured.');
        }

        $reference = $reference ?: 'PSK_'.Str::upper(Str::random(16));

        $payload = [
            'email' => $email,
            'amount' => $amountKobo,
            'currency' => $currency,
            'reference' => $reference,
            'metadata' => $metadata,
        ];

        if ($callbackUrl) {
            $payload['callback_url'] = $callbackUrl;
        }

        $response = Http::withToken($secret)
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', $payload)
            ->throw()
            ->json();

        if (! ($response['status'] ?? false)) {
            throw new \RuntimeException((string) ($response['message'] ?? 'Paystack initialization failed.'));
        }

        return [
            'reference' => $reference,
            'authorization_url' => (string) data_get($response, 'data.authorization_url'),
            'access_code' => (string) data_get($response, 'data.access_code'),
            'raw' => is_array($response) ? $response : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(string $reference): array
    {
        $secret = (string) config('services.paystack.secret_key');
        if ($secret === '') {
            throw new \RuntimeException('Paystack secret key is not configured.');
        }

        try {
            $response = Http::withToken($secret)
                ->acceptJson()
                ->get('https://api.paystack.co/transaction/verify/'.urlencode($reference))
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new \RuntimeException('Paystack verification failed.', 0, $e);
        }

        return is_array($response) ? $response : [];
    }
}
