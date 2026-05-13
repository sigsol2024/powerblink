<?php

namespace App\Services\Vpic;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Support\ListingOptionNormalizer;
use Illuminate\Support\Facades\Log;

final class VpicListingCatalogSyncService
{
    public function __construct(private readonly VpicClient $client) {}

    /**
     * @return array<string, int|string>
     */
    public function syncMakes(bool $dryRun = false): array
    {
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped_manual_duplicate' => 0, 'skipped_bad' => 0];
        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        if ($makeCatId <= 0) {
            return array_merge($stats, ['error' => 'Make category missing']);
        }

        $rows = $this->client->getResults('vehicles/GetAllMakes');
        foreach ($rows as $raw) {
            $payload = $this->normalizePayload($raw);
            $makeId = isset($payload['Make_ID']) ? trim((string) $payload['Make_ID']) : '';
            $makeName = isset($payload['Make_Name']) ? (string) $payload['Make_Name'] : '';
            if ($makeId === '' || trim($makeName) === '') {
                $stats['skipped_bad']++;

                continue;
            }
            $display = ListingOptionNormalizer::canonical('make', $makeName);
            if ($display === '') {
                $stats['skipped_bad']++;

                continue;
            }

            $vpicRow = ListingOption::query()
                ->where('category_id', $makeCatId)
                ->whereNull('parent_id')
                ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
                ->where('external_id', $makeId)
                ->first();

            if ($vpicRow) {
                if ($dryRun) {
                    $stats['updated']++;
                } else {
                    $vpicRow->update([
                        'value' => $display,
                        'last_synced_at' => now(),
                        'source_payload' => $payload,
                    ]);
                    $stats['updated']++;
                }

                continue;
            }

            if ($this->findManualRootByCanonicalValue($makeCatId, $display) !== null) {
                $stats['skipped_manual_duplicate']++;
                Log::info('vPIC sync: skipped make (manual duplicate)', ['make_id' => $makeId, 'value' => $display]);

                continue;
            }

            if ($dryRun) {
                $stats['inserted']++;

                continue;
            }

            $maxSort = (int) ListingOption::query()->where('category_id', $makeCatId)->whereNull('parent_id')->max('sort_order');
            ListingOption::query()->create([
                'category_id' => $makeCatId,
                'parent_id' => null,
                'value' => $display,
                'logo_path' => null,
                'flag_emoji' => null,
                'sort_order' => $maxSort + 1,
                'is_active' => true,
                'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
                'external_id' => $makeId,
                'last_synced_at' => now(),
                'source_payload' => $payload,
            ]);
            $stats['inserted']++;
        }

        return $stats;
    }

    /**
     * @return array<string, int|string>
     */
    public function syncModels(bool $dryRun = false): array
    {
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped_manual_duplicate' => 0, 'skipped_bad' => 0, 'parents_processed' => 0];
        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');
        if ($makeCatId <= 0 || $modelCatId <= 0) {
            return array_merge($stats, ['error' => 'Make/model category missing']);
        }

        $vpicMakes = ListingOption::query()
            ->where('category_id', $makeCatId)
            ->whereNull('parent_id')
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->whereNotNull('external_id')
            ->orderBy('id')
            ->get(['id', 'external_id', 'value']);

        foreach ($vpicMakes as $makeOption) {
            $makeApiId = trim((string) $makeOption->external_id);
            if ($makeApiId === '') {
                continue;
            }
            $stats['parents_processed']++;

            $this->client->delayBetweenRequests();
            $rows = $this->client->getResults('vehicles/GetModelsForMakeId/'.$makeApiId);

            foreach ($rows as $raw) {
                $payload = $this->normalizePayload($raw);
                $modelId = isset($payload['Model_ID']) ? trim((string) $payload['Model_ID']) : '';
                $modelName = isset($payload['Model_Name']) ? (string) $payload['Model_Name'] : '';
                if ($modelId === '' || trim($modelName) === '') {
                    $stats['skipped_bad']++;

                    continue;
                }
                $display = ListingOptionNormalizer::canonical('model', $modelName);
                if ($display === '') {
                    $stats['skipped_bad']++;

                    continue;
                }

                $vpicModel = ListingOption::query()
                    ->where('category_id', $modelCatId)
                    ->where('parent_id', $makeOption->id)
                    ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
                    ->where('external_id', $modelId)
                    ->first();

                if ($vpicModel) {
                    if ($dryRun) {
                        $stats['updated']++;
                    } else {
                        $vpicModel->update([
                            'value' => $display,
                            'last_synced_at' => now(),
                            'source_payload' => $payload,
                        ]);
                        $stats['updated']++;
                    }

                    continue;
                }

                if ($this->findManualChildByCanonicalValue($modelCatId, (int) $makeOption->id, $display) !== null) {
                    $stats['skipped_manual_duplicate']++;

                    continue;
                }

                if ($dryRun) {
                    $stats['inserted']++;

                    continue;
                }

                $maxSort = (int) ListingOption::query()
                    ->where('category_id', $modelCatId)
                    ->where('parent_id', $makeOption->id)
                    ->max('sort_order');

                ListingOption::query()->create([
                    'category_id' => $modelCatId,
                    'parent_id' => $makeOption->id,
                    'value' => $display,
                    'logo_path' => null,
                    'flag_emoji' => null,
                    'sort_order' => $maxSort + 1,
                    'is_active' => true,
                    'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
                    'external_id' => $modelId,
                    'last_synced_at' => now(),
                    'source_payload' => $payload,
                ]);
                $stats['inserted']++;
            }
        }

        return $stats;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizePayload(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        return $raw;
    }

    private function findManualRootByCanonicalValue(int $makeCatId, string $canonicalValue): ?int
    {
        $canonicalValue = trim($canonicalValue);
        if ($canonicalValue === '') {
            return null;
        }
        $lower = mb_strtolower($canonicalValue);

        $id = ListingOption::query()
            ->where('category_id', $makeCatId)
            ->whereNull('parent_id')
            ->whereNull('external_source')
            ->where(function ($q) use ($canonicalValue, $lower) {
                $q->where('value', $canonicalValue)->orWhereRaw('LOWER(value) = ?', [$lower]);
            })
            ->value('id');

        return $id ? (int) $id : null;
    }

    private function findManualChildByCanonicalValue(int $modelCatId, int $parentId, string $canonicalValue): ?int
    {
        $canonicalValue = trim($canonicalValue);
        if ($canonicalValue === '') {
            return null;
        }
        $lower = mb_strtolower($canonicalValue);

        $id = ListingOption::query()
            ->where('category_id', $modelCatId)
            ->where('parent_id', $parentId)
            ->whereNull('external_source')
            ->where(function ($q) use ($canonicalValue, $lower) {
                $q->where('value', $canonicalValue)->orWhereRaw('LOWER(value) = ?', [$lower]);
            })
            ->value('id');

        return $id ? (int) $id : null;
    }
}
