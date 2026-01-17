<?php
// File untuk memeriksa route API yang sebenarnya terdaftar

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Memeriksa Route API Volunteer Campaigns ===\n\n";

$routes = Route::getRoutes();

// Cari semua route yang mengandung volunteer-campaigns
foreach ($routes as $route) {
    if (strpos($route->uri(), 'volunteer-campaigns') !== false && strpos($route->uri(), 'api') !== false) {
        $methods = implode(',', $route->methods());
        echo sprintf("%-15s %s\n", $methods, $route->uri());
    }
}

echo "\n=== Memeriksa Route API Admin Lainnya ===\n";

// Cari semua route yang mengandung admin/volunteers
foreach ($routes as $route) {
    if ((strpos($route->uri(), 'admin/volunteers') !== false || strpos($route->uri(), 'admin/volunteer-campaigns-admin') !== false || strpos($route->uri(), 'admin/dashboard/overview') !== false) && strpos($route->uri(), 'api') !== false) {
        $methods = implode(',', $route->methods());
        echo sprintf("%-15s %s\n", $methods, $route->uri());
    }
}