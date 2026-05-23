<?php

namespace App\Support;

/**
 * Server-side price formatting aligned with {@see SiteCurrencyComposer} / main.js currency logic.
 */
final class CurrencyDisplay
{
    /**
     * @param  array<string, mixed>|null  $currencyUi
     */
    public static function formatAmount(float $amount, ?array $currencyUi = null, int $decimals = 0): string
    {
        $currencyUi = is_array($currencyUi) ? $currencyUi : [];
        $base = strtoupper((string) ($currencyUi['default'] ?? 'USD'));
        $selected = strtoupper((string) ($currencyUi['selected'] ?? $base));
        $symbols = is_array($currencyUi['symbols'] ?? null) ? $currencyUi['symbols'] : CurrencyCatalog::symbols();
        $rates = is_array($currencyUi['rates'] ?? null) ? $currencyUi['rates'] : [$base => 1.0];

        $rate = (float) ($rates[$selected] ?? 1.0);
        if ($rate <= 0 || ! is_finite($rate)) {
            $rate = $selected === $base ? 1.0 : 1.0;
        }

        $symbol = (string) ($symbols[$selected] ?? ($selected.' '));
        $converted = $amount * $rate;

        return $symbol.number_format($converted, max(0, $decimals), '.', ',');
    }
}
