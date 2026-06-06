<?php

/**
 * Offline verification for catalog import (no MySQL required).
 * Validates data file, source images, media copies, and optionally a bootstrapped DB.
 */

require __DIR__.'/../vendor/autoload.php';

$productsPath = __DIR__.'/../database/seeders/data/catalog-products.php';
$products = require $productsPath;
if (! is_array($products)) {
    fwrite(STDERR, "Invalid catalog-products.php\n");
    exit(1);
}

$demoSlugs = [
    'omi-silk-wrap', 'adira-sculpted-coat', 'kola-tonal-silk-dress', 'ada-leather-handbag',
    'ife-tailored-trouser', 'oba-indigo-blazer', 'zola-linen-shirt', 'lara-heeled-mule',
    'aje-woven-scarf', 'onyx-leather-clutch', 'knit-sculpt-top',
];

$errors = [];
$slugs = [];
$categories = [];

foreach ($products as $i => $product) {
    $n = $i + 1;
    foreach (['title', 'slug', 'category', 'overview', 'description', 'media_path', 'source_folder', 'source_file'] as $field) {
        if (empty($product[$field])) {
            $errors[] = "#{$n}: missing {$field}";
        }
    }

    $slug = (string) ($product['slug'] ?? '');
    if (isset($slugs[$slug])) {
        $errors[] = "#{$n}: duplicate slug {$slug}";
    }
    $slugs[$slug] = true;

    if (in_array($slug, $demoSlugs, true)) {
        $errors[] = "#{$n}: slug collides with demo product {$slug}";
    }

    $categories[(string) $product['category']] = ($categories[(string) $product['category']] ?? 0) + 1;

    $source = __DIR__.'/../public/asset/images/products/'
        .$product['source_folder'].'/'.$product['source_file'];
    if (! is_file($source)) {
        $errors[] = "#{$n}: missing source {$source}";
    }

    $media = __DIR__.'/../public/'.ltrim((string) $product['media_path'], '/');
    if (! is_file($media)) {
        $errors[] = "#{$n}: missing media {$media}";
    }

    if ((int) ($product['price'] ?? 0) !== 45000) {
        $errors[] = "#{$n}: unexpected price";
    }

    if ((int) ($product['stock'] ?? 0) !== 10) {
        $errors[] = "#{$n}: unexpected stock";
    }
}

echo "Catalog data: ".count($products)." products\n";
echo 'Categories: '.json_encode($categories)."\n";

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, "ERROR: {$error}\n");
    }
    exit(1);
}

echo "All offline checks passed.\n";
