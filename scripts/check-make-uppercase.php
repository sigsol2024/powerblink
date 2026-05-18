<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;

$catId = ListingOptionCategory::query()->where('slug', 'make')->value('id');

if (! $catId) {
    echo "No make category found.\n";
    exit(1);
}

$total = ListingOption::query()->where('category_id', $catId)->count();
$bad = ListingOption::query()
    ->where('category_id', $catId)
    ->whereRaw('BINARY value != BINARY UPPER(value)')
    ->count();

echo "Make options total: {$total}\n";
echo "Not fully uppercase: {$bad}\n";

if ($bad > 0) {
    $samples = ListingOption::query()
        ->where('category_id', $catId)
        ->whereRaw('BINARY value != BINARY UPPER(value)')
        ->limit(10)
        ->pluck('value');
    echo "Samples: ".implode(', ', $samples->all())."\n";
}
