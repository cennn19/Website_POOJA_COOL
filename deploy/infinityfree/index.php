<?php

/*
|--------------------------------------------------------------------------
| Front controller untuk InfinityFree
|--------------------------------------------------------------------------
|
| Struktur di hosting:
|   htdocs/            → isi folder public (index.php ini, build/, images/, ...)
|   htdocs/laravel/    → seluruh aplikasi Laravel (dikunci oleh .htaccess, tidak bisa diakses publik)
|
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$base = __DIR__.'/laravel';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $base.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $base.'/bootstrap/app.php';

// Folder publik adalah htdocs (folder file ini), bukan laravel/public.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
