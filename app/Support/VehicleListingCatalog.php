<?php

namespace App\Support;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Product filter options + listing option lookups. After the dress-store overhaul
 * the storefront only filters by product category, search, and price — the legacy
 * make/model/fuel/transmission etc. helpers have been retired.
 */
final class VehicleListingCatalog
{
    /**
     * @param  Collection<int, string>  $values
     * @return Collection<int, string>
     */
    public static function normalizeOptionValues(Collection $values): Collection
    {
        $byLower = [];
        foreach ($values as $value) {
            $trimmed = trim((string) $value);
            if ($trimmed === '') {
                continue;
            }
            $key = mb_strtolower($trimmed);
            if (! isset($byLower[$key])) {
                $byLower[$key] = $trimmed;
            }
        }
        natcasesort($byLower);

        return collect(array_values($byLower));
    }

    /**
     * Active root options for a category slug: id + value for selects.
     *
     * @return Collection<int, object{id:int, value:string}>
     */
    public static function activeRootOptionRows(string $slug): Collection
    {
        try {
            if (! Schema::hasTable('listing_options') || ! Schema::hasTable('listing_option_categories')) {
                return collect();
            }
        } catch (\Throwable) {
            return collect();
        }

        $catId = ListingOptionCategory::query()->where('slug', $slug)->value('id');
        if (! $catId) {
            return collect();
        }

        return ListingOption::query()
            ->where('category_id', $catId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('value')
            ->get(['id', 'value']);
    }

    /**
     * Returns the lean storefront filter option set. Only categories now.
     *
     * @return array<string, Collection<int, mixed>>
     */
    public static function filterOptions(): array
    {
        $categories = collect();
        try {
            if (Schema::hasTable('listing_options') && Schema::hasTable('listing_option_categories')) {
                $categories = self::activeRootOptionRows('product_category');
            }
        } catch (\Throwable) {
            // Fall through with empty collection.
        }

        return [
            'categories' => $categories,
        ];
    }

    /**
     * Active product-category option rows (id + value), used by the storefront filter
     * sidebar and the admin product form's category dropdown.
     *
     * @return Collection<int, object{id:int, value:string}>
     */
    public static function activeProductCategoryRows(): Collection
    {
        return self::activeRootOptionRows('product_category');
    }
}
