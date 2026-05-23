<?php

namespace App\Support;

use App\Models\SiteSetting;
use App\Services\CurrencyRateService;
use Illuminate\Support\Facades\View;

/**
 * Resolves header + price display currency for the current request (shared with Blade and JS).
 */
final class SiteCurrencyUi
{
    /** @var array<string, mixed>|null */
    private static ?array $resolved = null;

    /**
     * @return array<string, mixed>
     */
    public static function resolve(?array $site = null): array
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        if ($site === null) {
            $shared = View::shared('site');
            $site = is_array($shared) ? $shared : [];
        }
        if (! is_array($site) || trim((string) ($site['currency_code'] ?? '')) === '') {
            $site = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());
        }

        // Website currency is fixed to NGN (₦) everywhere.
        $defaultCurrency = 'NGN';

        $displayVersion = 1;
        // Admin default ALWAYS wins: no user/session/cookie/region overrides.
        $selectedCurrency = $defaultCurrency;
        $promptDismissed = true;
        $preferenceCurrent = true;

        $rates = [$defaultCurrency => 1.0];
        try {
            $rates = app(CurrencyRateService::class)->ratesFrom($defaultCurrency);
        } catch (\Throwable) {
            $rates = [$defaultCurrency => 1.0];
        }

        return self::$resolved = [
            'default' => $defaultCurrency,
            'selected' => $selectedCurrency,
            'supported' => CurrencyCatalog::supported(),
            'symbols' => CurrencyCatalog::symbols(),
            'rates' => $rates,
            'promptDismissed' => $promptDismissed,
            'displayVersion' => $displayVersion,
            'preferenceCurrent' => $preferenceCurrent,
        ];
    }

    public static function flush(): void
    {
        self::$resolved = null;
    }
}
