<?php

namespace Database\Seeders;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Seeds the apparel product catalog (table is still named `vehicles` for historical reasons).
 * Only writes the lean post-overhaul columns — no car-shaped legacy fields, no listing-option
 * sync. Phase 7 will wire category mapping after the additive migration ships.
 */
class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        $demoUsers = DemoData::users();
        $owner = User::query()->firstOrCreate(
            ['email' => DemoData::USER_EMAIL],
            [
                'name' => $demoUsers['user']['name'],
                'password' => Hash::make(DemoData::DEFAULT_PASSWORD),
                'email_verified_at' => now(),
            ]
        );
        $approver = User::query()->firstOrCreate(
            ['email' => DemoData::ADMIN_EMAIL],
            [
                'name' => $demoUsers['admin']['name'],
                'password' => Hash::make(DemoData::DEFAULT_PASSWORD),
                'email_verified_at' => now(),
            ]
        );

        if (! $owner->hasRole('user')) {
            $owner->assignRole('user');
        }
        if (! $approver->hasRole('admin')) {
            $approver->assignRole('admin');
        }

        $hasStockColumn = Schema::hasColumn('vehicles', 'stock');
        $hasCategoryColumn = Schema::hasColumn('vehicles', 'product_category_listing_option_id');
        $categoryLookup = $this->productCategoryLookup();

        foreach (DemoData::vehicles() as $product) {
            $slug = Str::slug($product['title']);

            $payload = [
                'user_id' => $owner->id,
                'title' => $product['title'],
                'slug' => $slug,
                'status' => 'approved',
                'price' => $product['price'],
                'features' => $product['features'] ?? null,
                'description' => $product['description'] ?? 'Placeholder product description.',
                'submitted_at' => now(),
                'approved_at' => now(),
                'approved_by' => $approver->id,
            ];

            if ($hasStockColumn) {
                $payload['stock'] = $product['stock'] ?? 0;
            }

            if ($hasCategoryColumn) {
                $categoryName = (string) ($product['category'] ?? '');
                $payload['product_category_listing_option_id'] = $categoryLookup[strtolower($categoryName)] ?? null;
            }

            $vehicle = Vehicle::query()->updateOrCreate(['slug' => $slug], $payload);

            VehicleImage::query()->where('vehicle_id', $vehicle->id)->delete();
            foreach ($product['images'] as $idx => $path) {
                VehicleImage::query()->create([
                    'vehicle_id' => $vehicle->id,
                    'path' => $path,
                    'sort_order' => $idx,
                ]);
            }
        }
    }

    /**
     * lowercased category-name -> listing_options.id (active rows under the
     * 'product_category' slug). Empty array when the catalogue hasn't been seeded.
     *
     * @return array<string, int>
     */
    private function productCategoryLookup(): array
    {
        if (! Schema::hasTable('listing_options') || ! Schema::hasTable('listing_option_categories')) {
            return [];
        }

        $categoryId = (int) ListingOptionCategory::query()->where('slug', 'product_category')->value('id');
        if ($categoryId === 0) {
            return [];
        }

        return ListingOption::query()
            ->where('category_id', $categoryId)
            ->whereNull('parent_id')
            ->pluck('id', 'value')
            ->mapWithKeys(fn ($id, $value) => [strtolower((string) $value) => (int) $id])
            ->all();
    }
}
