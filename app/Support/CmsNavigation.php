<?php

namespace App\Support;

use App\Models\CmsPage;
use Illuminate\Support\Facades\Schema;

/**
 * Public marketing nav visibility driven by cms_pages.is_active.
 */
final class CmsNavigation
{
    /** @var array<string, bool>|null */
    private static ?array $visibility = null;

    /** @return list<string> */
    public static function navSlugs(): array
    {
        return [
            'home',
            'programs',
            'coaching',
            'gallery',
            'tournaments',
            'about',
            'contact',
            'faq',
            'privacy-policy',
            'terms',
        ];
    }

    public static function isVisible(string $slug): bool
    {
        return (self::visibilityMap())[$slug] ?? true;
    }

    /** @return array<string, bool> */
    public static function visibilityMap(): array
    {
        if (self::$visibility !== null) {
            return self::$visibility;
        }

        $map = array_fill_keys(self::navSlugs(), true);

        try {
            if (Schema::hasTable((new CmsPage)->getTable())) {
                $rows = CmsPage::query()
                    ->whereIn('slug', self::navSlugs())
                    ->get(['slug', 'is_active']);

                foreach ($rows as $row) {
                    $map[$row->slug] = (bool) $row->is_active;
                }
            }
        } catch (\Throwable) {
            // Keep defaults (visible) if DB unavailable.
        }

        return self::$visibility = $map;
    }

    public static function flushCache(): void
    {
        self::$visibility = null;
    }
}
