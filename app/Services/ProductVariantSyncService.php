<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleVariant;
use Illuminate\Support\Collection;

class ProductVariantSyncService
{
    /**
     * @param  array<int, string>  $dimensions  e.g. ['size', 'color']
     * @param  array<string, array<int, int>>  $optionIdsByDimension  e.g. ['size' => [1,2], 'color' => [3]]
     * @param  array<string, array{stock?:int,sku?:string,price?:int}>  $matrix  keys: "sizeId_colorId" or "sizeId_" or "_colorId"
     */
    public function sync(Vehicle $vehicle, array $dimensions, array $optionIdsByDimension, array $matrix): void
    {
        $dimensions = array_values(array_intersect(['size', 'color'], $dimensions));

        if ($dimensions === []) {
            VehicleVariant::query()->where('vehicle_id', $vehicle->id)->delete();

            return;
        }

        $sizeIds = in_array('size', $dimensions, true)
            ? collect($optionIdsByDimension['size'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()
            : collect();
        $colorIds = in_array('color', $dimensions, true)
            ? collect($optionIdsByDimension['color'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()
            : collect();

        $combinations = $this->buildCombinations($sizeIds, $colorIds);
        $keepIds = [];

        foreach ($combinations as $combo) {
            $key = $this->matrixKey($combo['size_id'], $combo['color_id']);
            $row = $matrix[$key] ?? [];
            $stock = max(0, (int) ($row['stock'] ?? 0));

            $variant = VehicleVariant::query()->firstOrNew([
                'vehicle_id' => $vehicle->id,
                'size_listing_option_id' => $combo['size_id'],
                'color_listing_option_id' => $combo['color_id'],
            ]);

            $variant->stock = $stock;
            $variant->is_active = true;
            if (isset($row['sku']) && trim((string) $row['sku']) !== '') {
                $variant->sku = trim((string) $row['sku']);
            }
            if (isset($row['price']) && $row['price'] !== '') {
                $variant->price = (int) $row['price'];
            }
            $variant->save();
            $keepIds[] = $variant->id;
        }

        $stale = VehicleVariant::query()->where('vehicle_id', $vehicle->id);
        if ($keepIds !== []) {
            $stale->whereNotIn('id', $keepIds);
        }
        $stale->delete();
    }

    /**
     * @return Collection<int, array{size_id:?int,color_id:?int}>
     */
    private function buildCombinations(Collection $sizeIds, Collection $colorIds): Collection
    {
        if ($sizeIds->isNotEmpty() && $colorIds->isNotEmpty()) {
            return $sizeIds->flatMap(fn ($sizeId) => $colorIds->map(fn ($colorId) => [
                'size_id' => $sizeId,
                'color_id' => $colorId,
            ]));
        }

        if ($sizeIds->isNotEmpty()) {
            return $sizeIds->map(fn ($sizeId) => ['size_id' => $sizeId, 'color_id' => null]);
        }

        if ($colorIds->isNotEmpty()) {
            return $colorIds->map(fn ($colorId) => ['size_id' => null, 'color_id' => $colorId]);
        }

        return collect();
    }

    public function matrixKey(?int $sizeId, ?int $colorId): string
    {
        return ($sizeId ?? '').'_'.($colorId ?? '');
    }
}
