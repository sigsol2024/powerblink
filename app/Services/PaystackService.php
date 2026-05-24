<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    public function initialize(Order $order, string $email, string $callbackUrl): array
    {
        $secret = (string) config('services.paystack.secret_key');
        if ($secret === '') {
            throw new \RuntimeException('Paystack secret key is not configured.');
        }

        $reference = 'PSK_'.Str::upper(Str::random(16));

        $response = Http::withToken($secret)
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => (int) $order->total,
                'currency' => $order->currency,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ])
            ->throw()
            ->json();

        if (! ($response['status'] ?? false)) {
            throw new \RuntimeException((string) ($response['message'] ?? 'Paystack initialization failed.'));
        }

        return [
            'reference' => $reference,
            'authorization_url' => (string) data_get($response, 'data.authorization_url'),
            'access_code' => (string) data_get($response, 'data.access_code'),
            'raw' => $response,
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
