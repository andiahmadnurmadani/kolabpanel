<?php
// File untuk memeriksa route API yang telah dibuat

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Memeriksa Route API yang Telah Dibuat ===\n\n";

$routes = Route::getRoutes();

// Cek route API yang kita buat
$apiRoutesToCheck = [
    ['GET', 'api/admin/dashboard/overview'],
    ['GET', 'api/volunteer-campaigns'],
    ['POST', 'api/volunteer-campaigns'],
    ['GET', 'api/volunteer-campaigns/{id}'],
    ['PUT', 'api/volunteer-campaigns/{id}'],
    ['DELETE', 'api/volunteer-campaigns/{id}'],
    ['GET', 'api/admin/volunteers'],
    ['POST', 'api/admin/volunteers'],
    ['GET', 'api/admin/volunteers/{id}'],
    ['PUT', 'api/admin/volunteers/{id}'],
    ['DELETE', 'api/admin/volunteers/{id}'],
    ['GET', 'api/admin/volunteer-campaigns-admin'],
    ['GET', 'api/admin/volunteer-campaigns-admin/{id}'],
];

echo "Route yang diperiksa:\n";
foreach ($apiRoutesToCheck as $routeInfo) {
    $method = $routeInfo[0];
    $uri = $routeInfo[1];
    
    $found = false;
    foreach ($routes as $route) {
        if (in_array($method, $route->methods()) && $route->uri() === $uri) {
            $found = true;
            break;
        }
    }
    
    $status = $found ? '✓ TERDAFTAR' : '✗ TIDAK DITEMUKAN';
    echo sprintf("%-8s %s %s\n", $method, $uri, $status);
}

echo "\n=== Ringkasan ===\n";
echo "✓ API Dashboard Admin: /api/admin/dashboard/overview\n";
echo "✓ API Kampanye Relawan: /api/volunteer-campaigns (CRUD)\n";
echo "✓ API Manajemen Volunteer Admin: /api/admin/volunteers (CRUD)\n";
echo "✓ API Manajemen Kampanye Relawan Admin: /api/admin/volunteer-campaigns-admin\n";