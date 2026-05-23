<?php

namespace App\Support;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Visitor currency preference versioning — invalidates session/cookie overrides when admin changes site default.
 */
final class SiteCurrencyPreference
{
    public const VERSION_KEY = 'currency_display_version';

    public const COOKIE_CURRENCY = 'site_currency_pref';

    public const COOKIE_VERSION = 'site_currency_v';

    public const SESSION_CURRENCY = 'site_currency';

    public const SESSION_VERSION = 'site_currency_v';

    public static function displayVersion(array $site): int
    {
        $raw = $site[self::VERSION_KEY] ?? SiteSetting::getValue(self::VERSION_KEY, '1');

        return max(1, (int) $raw);
    }

    public static function bumpDisplayVersion(): int
    {
        $next = self::displayVersion(SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed())) + 1;
        SiteSetting::setValue(self::VERSION_KEY, (string) $next);

        return $next;
    }

    public static function clearRateCaches(string $oldCurrency, string $newCurrency): void
    {
        foreach (array_unique([strtoupper($oldCurrency), strtoupper($newCurrency)]) as $code) {
            if ($code !== '') {
                Cache::forget('fx_rates_'.$code);
            }
        }
    }

    /**
     * After admin changes site default currency: bump version, clear FX cache, align stored user prefs that matched the old default.
     */
    public static function applySiteDefaultCurrencyChange(string $oldCurrency, string $newCurrency): void
    {
        $old = strtoupper(trim($oldCurrency));
        $new = strtoupper(trim($newCurrency));
        if ($old === '' || $new === '' || $old === $new) {
            return;
        }

        self::bumpDisplayVersion();
        self::clearRateCaches($old, $new);

        User::query()
            ->where('preferred_currency', $old)
            ->update(['preferred_currency' => $new]);
    }

    public static function storeVisitorPreference(Request $request, string $currency, int $displayVersion): void
    {
        $currency = strtoupper(trim($currency));
        $request->session()->put(self::SESSION_CURRENCY, $currency);
        $request->session()->put(self::SESSION_VERSION, $displayVersion);
    }

    public static function visitorPreferenceVersion(Request $request): ?int
    {
        if ($request->hasSession() && $request->session()->has(self::SESSION_VERSION)) {
            return (int) $request->session()->get(self::SESSION_VERSION);
        }

        $cookie = $request->cookie(self::COOKIE_VERSION);
        if ($cookie !== null && $cookie !== '') {
            return (int) $cookie;
        }

        return null;
    }

    public static function visitorPreferenceIsCurrent(Request $request, int $displayVersion): bool
    {
        $stored = self::visitorPreferenceVersion($request);

        return $stored !== null && $stored === $displayVersion;
    }

    /**
     * @return array{currency: string, minutes: int}
     */
    public static function preferenceCookies(string $currency, int $displayVersion): array
    {
        $minutes = 60 * 24 * 365;

        return [
            ['name' => self::COOKIE_CURRENCY, 'value' => strtoupper(trim($currency)), 'minutes' => $minutes],
            ['name' => self::COOKIE_VERSION, 'value' => (string) $displayVersion, 'minutes' => $minutes],
        ];
    }
}
