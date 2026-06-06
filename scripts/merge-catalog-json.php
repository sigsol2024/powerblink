<?php

$files = [
    __DIR__.'/../database/seeders/data/catalog-african.json',
    __DIR__.'/../database/seeders/data/catalog-luxury.json',
    __DIR__.'/../database/seeders/data/catalog-ready.json',
];

$all = [];
foreach ($files as $file) {
    if (! is_file($file)) {
        fwrite(STDERR, "Missing: {$file}\n");
        exit(1);
    }
    $chunk = json_decode(file_get_contents($file), true);
    if (! is_array($chunk)) {
        fwrite(STDERR, "Invalid JSON: {$file}\n");
        exit(1);
    }
    $all = array_merge($all, $chunk);
}

$outJson = __DIR__.'/../database/seeders/data/catalog-products.json';
$outPhp = __DIR__.'/../database/seeders/data/catalog-products.php';

file_put_contents($outJson, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$export = var_export($all, true);
$php = "<?php\n\n/**\n * Catalog import product definitions (84 items).\n * Generated from visual image review — do not hand-edit unless correcting copy.\n */\nreturn {$export};\n";
file_put_contents($outPhp, $php);

echo 'Merged '.count($all)." products into catalog-products.json and catalog-products.php\n";
