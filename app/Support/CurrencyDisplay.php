<?php

namespace App\Support;

/**
 * Server-side price formatting aligned with {@see SiteCurrencyUi} / main.js currency logic.
 */
final class CurrencyDisplay
{
    public static function formatForSite(float $amount, int $decimals = 0): string
    {
        return self::formatAmount($amount, SiteCurrencyUi::resolve(), $decimals);
    }

    /**
     * @param  array<string, mixed>|null  $currencyUi
     */
    public static function formatAmount(float $amount, ?array $currencyUi = null, int $decimals = 0): string
    {
        if (! is_array($currencyUi) || ! isset($currencyUi['selected'])) {
            $currencyUi = SiteCurrencyUi::resolve();
        }

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
