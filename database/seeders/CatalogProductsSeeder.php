<?php

namespace Database\Seeders;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Database\Seeders\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Imports catalog products from public/asset/images/products/ into the storefront.
 * Idempotent: updateOrCreate by slug; preserves products not in the catalog data file.
 */
class CatalogProductsSeeder extends Seeder
{
    private const AFRICAN_ATTIRES_CATEGORY = 'African Attires';

    /** @var array<string, string> */
    private array $folderMap = [
        'african wrappers' => self::AFRICAN_ATTIRES_CATEGORY,
        'Luxury_Fabrics' => 'Luxury Fabrics',
        'ready_to_wear' => 'Ready-to-Wear',
    ];

    public function run(): void
    {
        $products = $this->catalogProducts();
        if ($products === []) {
            $this->command?->error('No catalog products found. Run scripts/merge-catalog-json.php after adding data files.');

            return;
        }

        $owner = $this->resolveOwner();
        $approver = $this->resolveApprover();
        $categoryLookup = $this->productCategoryLookup();

        $this->ensureAfricanAttiresCategory($categoryLookup);

        // Refresh lookup after possible insert.
        $categoryLookup = $this->productCategoryLookup();

        $hasStock = Schema::hasColumn('vehicles', 'stock');
        $hasCategory = Schema::hasColumn('vehicles', 'product_category_listing_option_id');
        $hasOverview = Schema::hasColumn('vehicles', 'overview');

        $created = 0;
        $updated = 0;

        foreach ($products as $product) {
            $slug = (string) ($product['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $sourceFolder = (string) ($product['source_folder'] ?? '');
            $sourceFile = (string) ($product['source_file'] ?? '');
            $categoryName = (string) ($product['category'] ?? ($this->folderMap[$sourceFolder] ?? ''));

            $mediaPath = $this->copyProductImage($sourceFolder, $sourceFile, $slug, (string) ($product['media_path'] ?? ''));

            $payload = [
                'user_id' => $owner->id,
                'title' => (string) ($product['title'] ?? Str::title(str_replace('-', ' ', $slug))),
                'slug' => $slug,
                'status' => 'approved',
                'price' => (int) ($product['price'] ?? 45000),
                'features' => $product['features'] ?? null,
                'description' => (string) ($product['description'] ?? ''),
                'vin' => 'VD-'.strtoupper(Str::slug($slug, '-')),
                'submitted_at' => now(),
                'approved_at' => now(),
                'approved_by' => $approver->id,
            ];

            if ($hasStock) {
                $payload['stock'] = (int) ($product['stock'] ?? 10);
            }

            if ($hasOverview && ! empty($product['overview'])) {
                $payload['overview'] = (string) $product['overview'];
            }

            if ($hasCategory) {
                $payload['product_category_listing_option_id'] = $categoryLookup[strtolower($categoryName)] ?? null;
            }

            $existing = Vehicle::query()->where('slug', $slug)->exists();
            $vehicle = Vehicle::query()->updateOrCreate(['slug' => $slug], $payload);

            VehicleImage::query()->where('vehicle_id', $vehicle->id)->delete();
            VehicleImage::query()->create([
                'vehicle_id' => $vehicle->id,
                'path' => $mediaPath,
                'sort_order' => 0,
            ]);

            if ($existing) {
                $updated++;
            } else {
                $created++;
            }
        }

        $this->command?->info("Catalog import complete: {$created} created, {$updated} updated, ".count($products).' total.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalogProducts(): array
    {
        $path = database_path('seeders/data/catalog-products.php');
        if (! is_file($path)) {
            return [];
        }

        $data = require $path;

        return is_array($data) ? $data : [];
    }

    private function resolveOwner(): User
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

        if (! $owner->hasRole('user')) {
            $owner->assignRole('user');
        }

        return $owner;
    }

    private function resolveApprover(): User
    {
        $demoUsers = DemoData::users();

        $approver = User::query()->firstOrCreate(
            ['email' => DemoData::ADMIN_EMAIL],
            [
                'name' => $demoUsers['admin']['name'],
                'password' => Hash::make(DemoData::DEFAULT_PASSWORD),
                'email_verified_at' => now(),
            ]
        );

        if (! $approver->hasRole('admin')) {
            $approver->assignRole('admin');
        }

        return $approver;
    }

    /**
     * @param  array<string, int>  $lookup
     */
    private function ensureAfricanAttiresCategory(array &$lookup): void
    {
        $key = strtolower(self::AFRICAN_ATTIRES_CATEGORY);
        if (isset($lookup[$key])) {
            return;
        }

        if (! Schema::hasTable('listing_options') || ! Schema::hasTable('listing_option_categories')) {
            return;
        }

        $categoryId = (int) ListingOptionCategory::query()->where('slug', 'product_category')->value('id');
        if ($categoryId === 0) {
            return;
        }

        $maxSort = (int) ListingOption::query()
            ->where('category_id', $categoryId)
            ->whereNull('parent_id')
            ->max('sort_order');

        ListingOption::query()->create([
            'category_id' => $categoryId,
            'parent_id' => null,
            'value' => self::AFRICAN_ATTIRES_CATEGORY,
            'sort_order' => $maxSort + 10,
            'is_active' => true,
        ]);
    }

    /**
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

    private function copyProductImage(string $sourceFolder, string $sourceFile, string $slug, string $preferredPath): string
    {
        $ext = pathinfo($sourceFile, PATHINFO_EXTENSION) ?: 'jpeg';
        $destRelative = $preferredPath !== ''
            ? ltrim($preferredPath, '/')
            : 'asset/images/media/'.$slug.'.'.strtolower($ext);

        $destAbsolute = public_path($destRelative);
        $sourceAbsolute = public_path('asset/images/products/'.$sourceFolder.'/'.$sourceFile);

        File::ensureDirectoryExists(dirname($destAbsolute));

        if (is_file($sourceAbsolute)) {
            if (! is_file($destAbsolute) || filemtime($sourceAbsolute) > filemtime($destAbsolute)) {
                File::copy($sourceAbsolute, $destAbsolute);
            }
        }

        return $destRelative;
    }
}
