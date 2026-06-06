<?php

/**
 * Copy catalog product images from products/ staging folders to asset/images/media/
 * without requiring a database connection. Safe to run before deploy.
 */

require __DIR__.'/../vendor/autoload.php';

$products = require __DIR__.'/../database/seeders/data/catalog-products.php';
if (! is_array($products)) {
    fwrite(STDERR, "Invalid catalog-products.php\n");
    exit(1);
}

$copied = 0;
$skipped = 0;
$missing = 0;

foreach ($products as $product) {
    $sourceFolder = (string) ($product['source_folder'] ?? '');
    $sourceFile = (string) ($product['source_file'] ?? '');
    $slug = (string) ($product['slug'] ?? '');
    $mediaPath = (string) ($product['media_path'] ?? '');

    if ($sourceFolder === '' || $sourceFile === '' || $slug === '') {
        continue;
    }

    $ext = pathinfo($sourceFile, PATHINFO_EXTENSION) ?: 'jpeg';
    $destRelative = $mediaPath !== ''
        ? ltrim($mediaPath, '/')
        : 'asset/images/media/'.$slug.'.'.strtolower($ext);

    $source = __DIR__.'/../public/asset/images/products/'.$sourceFolder.'/'.$sourceFile;
    $dest = __DIR__.'/../public/'.$destRelative;

    if (! is_file($source)) {
        fwrite(STDERR, "Missing source: {$source}\n");
        $missing++;
        continue;
    }

    $destDir = dirname($dest);
    if (! is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    if (is_file($dest) && filemtime($source) <= filemtime($dest)) {
        $skipped++;
        continue;
    }

    if (! copy($source, $dest)) {
        fwrite(STDERR, "Copy failed: {$source} -> {$dest}\n");
        exit(1);
    }

    $copied++;
}

echo "Catalog images: {$copied} copied, {$skipped} already up to date, {$missing} missing sources.\n";
echo 'Total products in catalog: '.count($products)."\n";

exit($missing > 0 ? 1 : 0);
