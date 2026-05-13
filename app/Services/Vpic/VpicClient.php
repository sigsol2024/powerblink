<?php

namespace App\Services\Vpic;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal HTTP client for NHTSA vPIC JSON endpoints (no API key).
 */
final class VpicClient
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getResults(string $path, array $query = []): array
    {
        $query = array_merge(['format' => 'json'], $query);
        $base = rtrim((string) config('vpic.base_url'), '/');
        $path = ltrim($path, '/');
        $url = $base.'/'.$path;

        $timeout = max(1, (int) config('vpic.timeout', 15));
        $retries = max(0, (int) config('vpic.retries', 3));
        $retrySleepMs = max(0, (int) config('vpic.retry_sleep_ms', 500));

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => (string) config('vpic.user_agent', 'Laravel/vPIC'),
        ])
            ->timeout($timeout)
            ->retry($retries, $retrySleepMs)
            ->get($url, $query);

        if (! $response->successful()) {
            throw new RuntimeException('vPIC HTTP '.$response->status().' for '.$url);
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('vPIC unexpected body for '.$url);
        }

        $results = $data['Results'] ?? null;
        if (! is_array($results)) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $out */
        $out = [];
        foreach ($results as $row) {
            if (is_array($row)) {
                $out[] = $row;
            }
        }

        return $out;
    }

    public function delayBetweenRequests(): void
    {
        $ms = max(0, (int) config('vpic.delay_ms', 300));
        if ($ms > 0) {
            usleep($ms * 1000);
        }
    }
}
