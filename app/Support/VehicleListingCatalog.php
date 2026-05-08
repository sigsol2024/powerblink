<?php

namespace App\Support;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Inventory filter options + dashboard listing form option lists (ID-based listing_option rows).
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
     * Active makes with logo/flag for public header mega menu (desktop).
     *
     * @return Collection<int, ListingOption>
     */
    public static function activeMakeNavTiles(): Collection
    {
        try {
            if (! Schema::hasTable('listing_options') || ! Schema::hasTable('listing_option_categories')) {
                return collect();
            }
        } catch (\Throwable) {
            return collect();
        }

        $catId = ListingOptionCategory::query()->where('slug', 'make')->value('id');
        if (! $catId) {
            return collect();
        }

        return ListingOption::query()
            ->where('category_id', $catId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('value')
            ->get(['id', 'value', 'logo_path', 'flag_emoji']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function filterOptions(): array
    {
        try {
            if (Schema::hasTable('listing_options') && Schema::hasTable('listing_option_categories')) {
                return self::fromListingCatalog();
            }
        } catch (\Throwable) {
            //
        }

        return [
            'makes' => collect(),
            'models' => collect(),
            'model_matrix' => collect(),
            'fuel_types' => collect(),
            'transmissions' => collect(),
            'body_types' => collect(),
            'drives' => collect(),
            'countries' => collect(),
            'vehicle_origin_types' => collect(),
            'conditions' => collect(),
            'exterior_colors' => self::normalizeOptionValues(
                Vehicle::query()->where('status', 'approved')->whereNotNull('exterior_color')->where('exterior_color', '!=', '')->pluck('exterior_color')
            ),
        ];
    }

    /**
     * Active root make options (id + value) for cascading model fetch.
     *
     * @return Collection<int, object{id:int, value:string}>
     */
    public static function activeMakeSelectRows(): Collection
    {
        return self::activeRootOptionRows('make');
    }

    /**
     * @param  Collection<int, array{make_id: int, model_id: int, make: string, model: string}>  $matrix
     */
    public static function assertMakeModelPairById(Collection $matrix, ?int $makeId, ?int $modelId): void
    {
        if ($makeId === null || $makeId === 0 || $modelId === null || $modelId === 0 || $matrix->isEmpty()) {
            return;
        }

        $ok = $matrix->contains(fn (array $r) => (int) ($r['make_id'] ?? 0) === $makeId && (int) ($r['model_id'] ?? 0) === $modelId);
        if (! $ok) {
            throw ValidationException::withMessages([
                'model_listing_option_id' => __('Model does not match the selected make.'),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function fromListingCatalog(): array
    {
        $modelCatId = ListingOptionCategory::query()->where('slug', 'model')->value('id');
        $matrix = collect();
        if ($modelCatId) {
            $matrix = ListingOption::query()
                ->where('category_id', $modelCatId)
                ->whereNotNull('parent_id')
                ->where('is_active', true)
                ->with('parent')
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get()
                ->map(fn (ListingOption $o) => [
                    'make_id' => (int) ($o->parent_id ?? 0),
                    'model_id' => (int) $o->id,
                    'make' => (string) ($o->parent?->value ?? ''),
                    'model' => $o->value,
                ])
                ->filter(fn (array $r) => $r['make_id'] > 0 && $r['model_id'] > 0);
        }

        $modelLabels = $matrix->pluck('model')->unique()->sort()->values();

        $optionQuery = Vehicle::query()->where('status', 'approved');

        return [
            'makes' => self::activeRootOptionRows('make'),
            'models' => $modelLabels,
            'model_matrix' => $matrix->values(),
            'fuel_types' => self::activeRootOptionRows('fuel_type'),
            'transmissions' => self::activeRootOptionRows('transmission'),
            'body_types' => self::activeRootOptionRows('body_type'),
            'drives' => self::activeRootOptionRows('drive'),
            'countries' => self::activeRootOptionRows('country'),
            'vehicle_origin_types' => self::activeRootOptionRows('vehicle_origin_type'),
            'conditions' => self::activeRootOptionRows('condition'),
            'exterior_colors' => self::normalizeOptionValues((clone $optionQuery)->whereNotNull('exterior_color')->where('exterior_color', '!=', '')->pluck('exterior_color')),
        ];
    }

    /**
     * Root country option id whose label matches "Nigeria" (case-insensitive), or null.
     */
    public static function nigeriaCountryListingOptionId(): ?int
    {
        $rows = self::activeRootOptionRows('country');
        foreach ($rows as $row) {
            if (mb_strtolower(trim((string) ($row->value ?? ''))) === 'nigeria') {
                return (int) $row->id;
            }
        }

        return null;
    }

    /**
     * Origin type option id for "Nigerian" / "Foreign" (case-insensitive value match).
     */
    public static function vehicleOriginTypeIdByLabel(string $label): ?int
    {
        $key = mb_strtolower(trim($label));
        if ($key === '') {
            return null;
        }
        $rows = self::activeRootOptionRows('vehicle_origin_type');
        foreach ($rows as $row) {
            if (mb_strtolower(trim((string) ($row->value ?? ''))) === $key) {
                return (int) $row->id;
            }
        }

        return null;
    }
}
