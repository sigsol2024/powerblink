<?php

namespace App\Services\Vpic;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class VpicListingCatalogPruneService
{
    /**
     * Hard-delete unused vPIC catalog rows outside the curated Make_ID allowlist.
     * Manual rows (external_source null) are never deleted. vPIC models under junk makes go first,
     * then vPIC makes only when no vehicle references the make or any descendant model, and no manual model children exist.
     *
     * @return array{models_deleted: int, makes_deleted: int, error?: string}
     */
    public function prune(bool $dryRun = false): array
    {
        $allowed = $this->allowedMakeExternalIdStrings();
        if ($allowed === []) {
            return ['models_deleted' => 0, 'makes_deleted' => 0, 'error' => 'vpic.allowed_make_ids is empty; refusing to prune.'];
        }

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');
        if ($makeCatId <= 0 || $modelCatId <= 0) {
            return ['models_deleted' => 0, 'makes_deleted' => 0, 'error' => 'Make/model category missing'];
        }

        $nonAllowlistedVpicMakeIds = ListingOption::query()
            ->where('category_id', $makeCatId)
            ->whereNull('parent_id')
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->whereNotNull('external_id')
            ->whereNotIn('external_id', $allowed)
            ->pluck('id')
            ->all();

        $modelQuery = ListingOption::query()
            ->where('category_id', $modelCatId)
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->whereIn('parent_id', $nonAllowlistedVpicMakeIds)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('vehicles')
                    ->whereColumn('vehicles.model_listing_option_id', 'listing_options.id');
            });

        $makeQuery = $this->deletableNonAllowlistedVpicMakeQuery($makeCatId, $modelCatId, $allowed);

        if ($dryRun) {
            return [
                'models_deleted' => (int) (clone $modelQuery)->count(),
                'makes_deleted' => (int) (clone $makeQuery)->count(),
            ];
        }

        $modelsDeleted = (int) $modelQuery->delete();
        $makesDeleted = (int) $makeQuery->delete();

        return [
            'models_deleted' => $modelsDeleted,
            'makes_deleted' => $makesDeleted,
        ];
    }

    /**
     * @param  list<string>  $allowed
     */
    private function deletableNonAllowlistedVpicMakeQuery(int $makeCatId, int $modelCatId, array $allowed): Builder
    {
        return ListingOption::query()
            ->where('listing_options.category_id', $makeCatId)
            ->whereNull('listing_options.parent_id')
            ->where('listing_options.external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->whereNotNull('listing_options.external_id')
            ->whereNotIn('listing_options.external_id', $allowed)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('vehicles')
                    ->whereColumn('vehicles.make_listing_option_id', 'listing_options.id');
            })
            ->whereNotExists(function ($q) use ($modelCatId) {
                $q->select(DB::raw('1'))
                    ->from('listing_options as c')
                    ->whereColumn('c.parent_id', 'listing_options.id')
                    ->where('c.category_id', $modelCatId)
                    ->whereNull('c.external_source');
            })
            ->whereNotExists(function ($q) use ($modelCatId) {
                $q->select(DB::raw('1'))
                    ->from('listing_options as c')
                    ->join('vehicles', 'vehicles.model_listing_option_id', '=', 'c.id')
                    ->whereColumn('c.parent_id', 'listing_options.id')
                    ->where('c.category_id', $modelCatId);
            });
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
