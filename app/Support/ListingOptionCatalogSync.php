<?php

namespace App\Support;

/**
 * Legacy car-marketplace listing-option synchroniser, retained as a no-op shell so historical
 * migrations (notably 2026_05_02_120200_vehicles_use_listing_option_foreign_keys) can still
 * import and call its methods after the apparel pivot.
 *
 * Phase 6 of the overhaul stripped all live references to the legacy car FK columns.
 * Phase 9 then drops those columns from the schema entirely.
 */
final class ListingOptionCatalogSync
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function ensureOptionValuesFromArray(array $data): void
    {
    }

    public static function syncOptionsFromLegacyVehicleColumns(): void
    {
    }

    public static function ensureFallbackCountryForEmptyLegacyVehicleRows(): void
    {
    }

    /**
     * @param  object  $row
     * @return list<string>
     */
    public static function unresolvedLegacyProblems(object $row): array
    {
        return [];
    }

    /**
     * @param  object  $row
     * @return array<string, mixed>
     */
    public static function resolveLegacyRowToForeignKeys(object $row): array
    {
        return [];
    }

    public static function activeRootOptionCount(string $categorySlug): int
    {
        return 0;
    }
}
