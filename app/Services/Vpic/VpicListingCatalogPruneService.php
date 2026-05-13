<?php

namespace App\Services\Vpic;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use Illuminate\Support\Facades\DB;

final class VpicListingCatalogPruneService
{
    /**
     * Deactivate unused vPIC catalog rows outside the curated Make_ID allowlist.
     * Manual rows (external_source null) are never touched.
     *
     * @return array{models_deactivated: int, makes_deactivated: int, error?: string}
     */
    public function prune(bool $dryRun = false): array
    {
        $allowed = $this->allowedMakeExternalIdStrings();
        if ($allowed === []) {
            return ['models_deactivated' => 0, 'makes_deactivated' => 0, 'error' => 'vpic.allowed_make_ids is empty; refusing to prune.'];
        }

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');
        if ($makeCatId <= 0 || $modelCatId <= 0) {
            return ['models_deactivated' => 0, 'makes_deactivated' => 0, 'error' => 'Make/model category missing'];
        }

        $nonAllowlistedVpicMakeIds = ListingOption::query()
            ->where('category_id', $makeCatId)
            ->whereNull('parent_id')
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->whereNotNull('external_id')
            ->whereNotIn('external_id', $allowed)
            ->pluck('id')
            ->all();

        $modelsDeactivated = 0;
        $modelQuery = ListingOption::query()
            ->where('category_id', $modelCatId)
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->where('is_active', true)
            ->whereIn('parent_id', $nonAllowlistedVpicMakeIds)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('vehicles')
                    ->whereColumn('vehicles.model_listing_option_id', 'listing_options.id');
            });

        if ($dryRun) {
            $modelsDeactivated = (int) (clone $modelQuery)->count();
        } else {
            $modelsDeactivated = $modelQuery->update(['is_active' => false]);
        }

        $makesDeactivated = 0;
        $makeQuery = ListingOption::query()
            ->where('category_id', $makeCatId)
            ->whereNull('parent_id')
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->where('is_active', true)
            ->whereNotNull('external_id')
            ->whereNotIn('external_id', $allowed)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('vehicles')
                    ->whereColumn('vehicles.make_listing_option_id', 'listing_options.id');
            });

        if ($dryRun) {
            $makesDeactivated = (int) (clone $makeQuery)->count();
        } else {
            $makesDeactivated = $makeQuery->update(['is_active' => false]);
        }

        return [
            'models_deactivated' => $modelsDeactivated,
            'makes_deactivated' => $makesDeactivated,
        ];
    }

    /**
     * @return list<string>
     */
    private function allowedMakeExternalIdStrings(): array
    {
        $raw = config('vpic.allowed_make_ids', []);
        if (! is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $id) {
            $s = trim((string) $id);
            if ($s !== '' && ctype_digit($s)) {
                $out[] = $s;
            }
        }

        return array_values(array_unique($out));
    }
}
