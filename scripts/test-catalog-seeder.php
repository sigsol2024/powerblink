<?php

/**
 * End-to-end CatalogProductsSeeder smoke test using a temporary SQLite database.
 * Does not touch .env or production MySQL.
 */

$root = dirname(__DIR__);
$sqlite = $root.'/database/catalog_seeder_test.sqlite';

if (is_file($sqlite)) {
    unlink($sqlite);
}

touch($sqlite);

putenv('APP_ENV=local');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.$sqlite);

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Fresh schema (ignore migration that fails on SQLite legacy column drops).
try {
    $kernel->call('migrate', ['--force' => true]);
} catch (Throwable $e) {
    // Continue if vehicles table exists — enough for catalog import.
    if (! Illuminate\Support\Facades\Schema::hasTable('vehicles')) {
        fwrite(STDERR, 'Migration failed and vehicles table missing: '.$e->getMessage().PHP_EOL);
        exit(1);
    }
    fwrite(STDERR, 'Migration warning (continuing): '.$e->getMessage().PHP_EOL);
}

$kernel->call('db:seed', ['--class' => 'Database\\Seeders\\RolesSeeder', '--force' => true]);

$catId = (int) DB::table('listing_option_categories')->where('slug', 'product_category')->value('id');
$now = now();
foreach (['Luxury Fabrics' => 20, 'Ready-to-Wear' => 30] as $value => $sort) {
    if (! DB::table('listing_options')->where('category_id', $catId)->where('value', $value)->exists()) {
        DB::table('listing_options')->insert([
            'category_id' => $catId,
            'parent_id' => null,
            'value' => $value,
            'sort_order' => $sort,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

$kernel->call('db:seed', ['--class' => 'Database\\Seeders\\CatalogProductsSeeder', '--force' => true]);

$vehicles = (int) App\Models\Vehicle::query()->count();
$approved = (int) App\Models\Vehicle::query()->where('status', 'approved')->count();
$images = (int) App\Models\VehicleImage::query()->count();
$withCategory = (int) App\Models\Vehicle::query()->whereNotNull('product_category_listing_option_id')->count();
$african = App\Models\ListingOption::query()->where('value', 'African Attires')->exists();

$byCat = App\Models\Vehicle::query()
    ->join('listing_options', 'vehicles.product_category_listing_option_id', '=', 'listing_options.id')
    ->selectRaw('listing_options.value, count(*) as c')
    ->groupBy('listing_options.value')
    ->pluck('c', 'listing_options.value')
    ->all();

$errors = [];
if ($vehicles !== 84) {
    $errors[] = "Expected 84 vehicles, got {$vehicles}";
}
if ($approved !== 84) {
    $errors[] = "Expected 84 approved, got {$approved}";
}
if ($images !== 84) {
    $errors[] = "Expected 84 vehicle_images, got {$images}";
}
if ($withCategory !== 84) {
    $errors[] = "Expected 84 with category, got {$withCategory}";
}
if (! $african) {
    $errors[] = 'African Attires category was not created';
}
if (($byCat['African Attires'] ?? 0) !== 18) {
    $errors[] = 'Expected 18 African Attires products';
}
if (($byCat['Luxury Fabrics'] ?? 0) !== 34) {
    $errors[] = 'Expected 34 Luxury Fabrics products';
}
if (($byCat['Ready-to-Wear'] ?? 0) !== 32) {
    $errors[] = 'Expected 32 Ready-to-Wear products';
}

// Idempotent re-run
$before = $vehicles;
$kernel->call('db:seed', ['--class' => 'Database\\Seeders\\CatalogProductsSeeder', '--force' => true]);
$after = (int) App\Models\Vehicle::query()->count();
if ($after !== $before) {
    $errors[] = "Re-run changed vehicle count: {$before} -> {$after}";
}

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, "FAIL: {$error}\n");
    }
    exit(1);
}

echo "PASS: CatalogProductsSeeder imported 84 products with correct categories.\n";
echo 'By category: '.json_encode($byCat)."\n";

// SQLite file may still be locked by PDO; leave temp file for manual cleanup if needed.
if (is_file($sqlite)) {
    @unlink($sqlite);
}
