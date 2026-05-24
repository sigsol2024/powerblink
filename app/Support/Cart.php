<?php

namespace App\Support;

use App\Models\Vehicle;
use App\Models\VehicleVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

final class Cart
{
    private const KEY = 'cart.lines';

    /**
     * @return array<int, array{
     *   vehicle_id: int,
     *   vehicle_variant_id?: int|null,
     *   name: string,
     *   unit_price: int,
     *   qty: int,
     *   image: string|null,
     *   sku?: string|null,
     *   variant_label?: string|null
     * }>
     */
    public static function lines(): array
    {
        /** @var array<int, array<string, mixed>> $raw */
        $raw = Session::get(self::KEY, []);

        return array_values(array_filter($raw, fn ($line) => is_array($line) && ! empty($line['vehicle_id'])));
    }

    public static function count(): int
    {
        return array_sum(array_map(fn (array $line) => max(1, (int) ($line['qty'] ?? 1)), self::lines()));
    }

    public static function itemCount(): int
    {
        return count(self::lines());
    }

    public static function subtotal(): int
    {
        $total = 0;
        foreach (self::lines() as $line) {
            $total += (int) ($line['unit_price'] ?? 0) * max(1, (int) ($line['qty'] ?? 1));
        }

        return $total;
    }

    public static function add(int $vehicleId, int $qty = 1, ?int $variantId = null): void
    {
        $qty = max(1, min(99, $qty));

        $vehicle = Vehicle::query()
            ->with(['images', 'variants.sizeOption', 'variants.colorOption'])
            ->where('status', 'approved')
            ->whereKey($vehicleId)
            ->firstOrFail();

        $activeVariants = $vehicle->variants->filter(fn (VehicleVariant $v) => $v->is_active);
        $variant = null;

        if ($activeVariants->isNotEmpty()) {
            if ($variantId === null) {
                throw ValidationException::withMessages([
                    'vehicle_variant_id' => [__('Please select a size and color.')],
                ]);
            }

            $variant = $activeVariants->firstWhere('id', $variantId);
            if (! $variant) {
                throw ValidationException::withMessages([
                    'vehicle_variant_id' => [__('Selected variant is not available.')],
                ]);
            }

            if ((int) $variant->stock < $qty) {
                throw ValidationException::withMessages([
                    'qty' => [__('Not enough stock for this variant.')],
                ]);
            }
        }

        $unitPrice = $variant
            ? $variant->unitPriceNaira($vehicle)
            : max(0, (int) ($vehicle->price ?? 0));

        if ($unitPrice <= 0) {
            throw ValidationException::withMessages([
                'vehicle_id' => [__('This product is not available for purchase.')],
            ]);
        }

        $lineKey = self::lineKey($vehicleId, $variant?->id);

        $lines = self::lines();
        foreach ($lines as $index => $line) {
            if (self::lineKey((int) $line['vehicle_id'], isset($line['vehicle_variant_id']) ? (int) $line['vehicle_variant_id'] : null) === $lineKey) {
                $newQty = max(1, min(99, (int) ($line['qty'] ?? 1) + $qty));
                if ($variant && (int) $variant->stock < $newQty) {
                    throw ValidationException::withMessages([
                        'qty' => [__('Not enough stock for this variant.')],
                    ]);
                }
                $lines[$index]['qty'] = $newQty;
                Session::put(self::KEY, $lines);

                return;
            }
        }

        $cover = $vehicle->images->first();
        $image = $cover ? VehicleImageUrl::url($cover->path) : null;

        $lines[] = [
            'vehicle_id' => $vehicleId,
            'vehicle_variant_id' => $variant?->id,
            'name' => (string) ($vehicle->title ?? 'Product'),
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'image' => $image,
            'sku' => $variant?->sku,
            'variant_label' => $variant?->displayLabel(),
        ];

        Session::put(self::KEY, $lines);
    }

    public static function updateQty(int $vehicleId, int $qty, ?int $variantId = null): void
    {
        $qty = max(1, min(99, $qty));
        $lineKey = self::lineKey($vehicleId, $variantId);

        $lines = self::lines();
        foreach ($lines as $index => $line) {
            $existingVariant = isset($line['vehicle_variant_id']) ? (int) $line['vehicle_variant_id'] : null;
            if (self::lineKey((int) $line['vehicle_id'], $existingVariant) !== $lineKey) {
                continue;
            }

            if ($existingVariant) {
                $variant = VehicleVariant::query()
                    ->where('vehicle_id', $vehicleId)
                    ->whereKey($existingVariant)
                    ->where('is_active', true)
                    ->first();
                if ($variant && (int) $variant->stock < $qty) {
                    throw ValidationException::withMessages([
                        'qty' => [__('Not enough stock for this variant.')],
                    ]);
                }
            }

            $lines[$index]['qty'] = $qty;
            Session::put(self::KEY, $lines);

            return;
        }
    }

    public static function remove(int $vehicleId, ?int $variantId = null): void
    {
        $lineKey = self::lineKey($vehicleId, $variantId);
        $lines = array_values(array_filter(
            self::lines(),
            fn (array $line) => self::lineKey(
                (int) ($line['vehicle_id'] ?? 0),
                isset($line['vehicle_variant_id']) ? (int) $line['vehicle_variant_id'] : null
            ) !== $lineKey
        ));
        Session::put(self::KEY, $lines);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    private static function lineKey(int $vehicleId, ?int $variantId): string
    {
        return $vehicleId.':'.($variantId ?? 0);
    }
}
