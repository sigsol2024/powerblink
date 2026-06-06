<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $catId = App\Models\ListingOptionCategory::query()->where('slug', 'product_category')->value('id');
    $categories = App\Models\ListingOption::query()
        ->where('category_id', $catId)
        ->orderBy('sort_order')
        ->get(['id', 'value', 'is_active']);
    $vehicles = App\Models\Vehicle::query()
        ->orderBy('title')
        ->get(['id', 'title', 'slug', 'status']);
    echo json_encode([
        'categories' => $categories,
        'vehicles' => $vehicles,
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    fwrite(STDERR, 'ERROR: '.$e->getMessage().PHP_EOL);
    exit(1);
}
