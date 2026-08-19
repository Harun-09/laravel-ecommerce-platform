<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fix subdirectory deployment: normalize SCRIPT_NAME so Laravel
// correctly strips the /e-commerce/public prefix from REQUEST_URI.
if (strpos($_SERVER['REQUEST_URI'], '/e-commerce/public') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/e-commerce/public/index.php';
    $_SERVER['PHP_SELF'] = '/e-commerce/public/index.php';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
