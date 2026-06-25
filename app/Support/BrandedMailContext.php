<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

/**
 * Site branding for transactional HTML emails (OTP, password reset).
 */
final class BrandedMailContext
{
    /**
     * @return array{site: array<string, string>, siteName: string, logoUrl: ?string}
     */
    public static function forEmail(): array
    {
        $fromDb = [];
        try {
            if (Schema::hasTable('site_settings')) {
                $fromDb = SiteSetting::allKeyed();
            }
        } catch (\Throwable) {
            $fromDb = [];
        }
        $site = SiteSettingDefaults::mergeWithDatabase($fromDb);
        $siteName = ! empty(trim($site['site_display_name'] ?? ''))
            ? trim($site['site_display_name'])
            : (string) config('app.name', 'Laravel');
        $logoPath = trim((string) ($site['logo_path'] ?? ''));
        $logoUrl = $logoPath !== '' ? PlaceholderMedia::url($logoPath) : null;

        return [
            'site' => $site,
            'siteName' => $siteName,
            'logoUrl' => $logoUrl,
        ];
    }
}
