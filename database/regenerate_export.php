<?php

$_SERVER['APP_ENV'] = 'local';
$_ENV['APP_ENV'] = 'local';
putenv('APP_ENV=local');
$_SERVER['BOOTSTRAP_ADMIN_PASSWORD'] = 'password';
$_ENV['BOOTSTRAP_ADMIN_PASSWORD'] = 'password';
putenv('BOOTSTRAP_ADMIN_PASSWORD=password');
$_SERVER['DEMO_USER_PASSWORD'] = 'password';
$_ENV['DEMO_USER_PASSWORD'] = 'password';
putenv('DEMO_USER_PASSWORD=password');

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exit = Illuminate\Support\Facades\Artisan::call('db:export-powerblink-sql', ['--sqlite' => true, '--fresh' => true]);
$output = Illuminate\Support\Facades\Artisan::output();
Illuminate\Support\Facades\File::put(__DIR__.'/regenerate_export.log', $output."\nEXIT:{$exit}\n");
fwrite(STDOUT, $output);
exit($exit);
