<?php

namespace App\Support;

final class SiteBrand
{
    public const DEFAULT_NAME = 'Vogue Dress 4Less';

    public static function displayName(?array $site = null): string
    {
        $site = $site ?? (view()->shared('site') ?? []);
        $fromSettings = trim((string) ($site['site_display_name'] ?? ''));

        if ($fromSettings !== '') {
            return $fromSettings;
        }

        $fromConfig = trim((string) config('app.name', ''));

        if ($fromConfig !== '' && ! self::isLegacyAutoTorqueName($fromConfig)) {
            return $fromConfig;
        }

        return self::DEFAULT_NAME;
    }

    private static function isLegacyAutoTorqueName(string $name): bool
    {
        return str_contains(strtolower($name), 'auto torque');
    }
}
