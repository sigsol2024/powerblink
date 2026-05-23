<?php

namespace App\Support;

use App\Models\SiteSetting;
use App\Models\User;
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

        $defaultCurrency = strtoupper(trim((string) ($site['currency_code'] ?? 'USD')));
        if (! array_key_exists($defaultCurrency, CurrencyCatalog::supported())) {
            $defaultCurrency = 'USD';
        }

        $displayVersion = SiteCurrencyPreference::displayVersion($site);
        $selectedCurrency = $defaultCurrency;
        $promptDismissed = false;
        $preferenceCurrent = false;

        try {
            $cookieRaw = request()->cookie(SiteCurrencyPreference::COOKIE_CURRENCY);
            $cookieCurrency = strtoupper(trim((string) ($cookieRaw ?? '')));
            if ($cookieCurrency !== '' && ! array_key_exists($cookieCurrency, CurrencyCatalog::supported())) {
                $cookieCurrency = '';
            }

            /** @var User|null $authUser */
            $authUser = request()->user();
            $userCurrency = $authUser ? strtoupper(trim((string) ($authUser->preferred_currency ?? ''))) : '';
            if ($userCurrency !== '' && ! array_key_exists($userCurrency, CurrencyCatalog::supported())) {
                $userCurrency = '';
            }

            if (request()->hasSession()) {
                $promptDismissed = (bool) request()->session()->get('currency_selection_prompt_dismissed', false);
            }

            if ($authUser) {
                $promptDismissed = (bool) $authUser->currency_selection_prompt_dismissed;
            }

            $preferenceCurrent = SiteCurrencyPreference::visitorPreferenceIsCurrent(request(), $displayVersion);

            if ($userCurrency !== '') {
                $selectedCurrency = $userCurrency;
                if (request()->hasSession()) {
                    SiteCurrencyPreference::storeVisitorPreference(request(), $userCurrency, $displayVersion);
                }
            } elseif ($preferenceCurrent && request()->hasSession() && request()->session()->has(SiteCurrencyPreference::SESSION_CURRENCY)) {
                $sessionCurrency = strtoupper(trim((string) request()->session()->get(SiteCurrencyPreference::SESSION_CURRENCY)));
                if ($sessionCurrency !== '' && array_key_exists($sessionCurrency, CurrencyCatalog::supported())) {
                    $selectedCurrency = $sessionCurrency;
                }
            } elseif ($preferenceCurrent && $cookieCurrency !== '') {
                $selectedCurrency = $cookieCurrency;
                if (request()->hasSession()) {
                    SiteCurrencyPreference::storeVisitorPreference(request(), $cookieCurrency, $displayVersion);
                }
            } elseif (request()->hasSession()) {
                request()->session()->forget([
                    SiteCurrencyPreference::SESSION_CURRENCY,
                    SiteCurrencyPreference::SESSION_VERSION,
                ]);
            }
        } catch (\Throwable) {
            $selectedCurrency = $defaultCurrency;
        }

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
