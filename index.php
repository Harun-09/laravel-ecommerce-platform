<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file redirects all requests to the public folder.
 * For security, only the public folder should be web-accessible.
 */

// --- SERVER LOGIC (Commented) ---
/*
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Http\Request;
$app->handleRequest(Request::capture());
*/

// --- LOCALHOST LOGIC (Enabled) ---
header('Location: public/');
exit;
