<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = require __DIR__.'/../database/seeders/data/catalog-products.php';
$demoSlugs = array_map(
    fn (array $p) => Illuminate\Support\Str::slug($p['title']),
    Database\Seeders\DemoData::vehicles()
);

$lines = [];
$lines[] = '# Catalog Import Preview';
$lines[] = '';
$lines[] = 'Generated for review before `CatalogProductsSeeder` import.';
$lines[] = '';
$lines[] = '## Summary';
$lines[] = '';
$lines[] = '| Metric | Value |';
$lines[] = '|--------|-------|';
$lines[] = '| Total products | **'.count($products).'** |';
$lines[] = '| Default price | **₦45,000** |';
$lines[] = '| Default stock | **10** |';
$lines[] = '| Existing demo products | **Preserved** (additive import) |';
$lines[] = '| African Attires category | **Created by seeder if missing** |';
$lines[] = '';
$lines[] = '### Categories used';
$lines[] = '';

$byCat = [];
foreach ($products as $p) {
    $byCat[$p['category'] ?? 'Unknown'] = ($byCat[$p['category'] ?? 'Unknown'] ?? 0) + 1;
}
foreach ($byCat as $cat => $count) {
    $lines[] = "- **{$cat}**: {$count} products";
}
$lines[] = '';
$lines[] = '### Source folders';
$lines[] = '';
$byFolder = [];
foreach ($products as $p) {
    $byFolder[$p['source_folder'] ?? ''] = ($byFolder[$p['source_folder'] ?? ''] ?? 0) + 1;
}
foreach ($byFolder as $folder => $count) {
    $lines[] = "- `{$folder}`: {$count} images";
}
$lines[] = '';
$lines[] = '## Product catalog';
$lines[] = '';
$lines[] = '| # | Folder | File | Title | Slug | Category | Media path | Collision? |';
$lines[] = '|---|--------|------|-------|------|----------|------------|------------|';

$i = 0;
foreach ($products as $p) {
    $i++;
    $collision = in_array($p['slug'], $demoSlugs, true) ? 'Yes (demo)' : 'No';
    $lines[] = sprintf(
        '| %d | %s | %s | %s | `%s` | %s | `%s` | %s |',
        $i,
        str_replace('|', '\\|', (string) ($p['source_folder'] ?? '')),
        str_replace('|', '\\|', (string) ($p['source_file'] ?? '')),
        str_replace('|', '\\|', (string) ($p['title'] ?? '')),
        $p['slug'] ?? '',
        str_replace('|', '\\|', (string) ($p['category'] ?? '')),
        $p['media_path'] ?? '',
        $collision
    );
}

$lines[] = '';
$lines[] = '## Copy & descriptions';
$lines[] = '';
foreach ($products as $idx => $p) {
    $n = $idx + 1;
    $lines[] = "### {$n}. {$p['title']}";
    $lines[] = '';
    $lines[] = '**Overview:** '.($p['overview'] ?? '');
    $lines[] = '';
    $lines[] = '**Description:** '.($p['description'] ?? '');
    $lines[] = '';
    $lines[] = '**Features:**';
    foreach ($p['features'] ?? [] as $feature) {
        $lines[] = '- '.$feature;
    }
    $lines[] = '';
}

$out = __DIR__.'/../docs/catalog-import-preview.md';
file_put_contents($out, implode("\n", $lines));
echo "Wrote {$out} ({$i} products)\n";
