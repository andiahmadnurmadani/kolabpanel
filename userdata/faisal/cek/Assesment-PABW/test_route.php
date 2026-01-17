<?php
// File untuk menguji route akses ke storage

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

// Cek apakah route kita terdaftar
$routes = Route::getRoutes();
$storageRoute = null;

foreach ($routes as $route) {
    if (strpos($route->uri(), 'storage/{folder}/{filename}') !== false) {
        $storageRoute = $route;
        break;
    }
}

if ($storageRoute) {
    echo "Route ditemukan: " . $storageRoute->uri() . "\n";
    echo "Method: " . implode(', ', $storageRoute->methods()) . "\n";
} else {
    echo "Route tidak ditemukan!\n";
}

// Coba buat URL untuk beberapa file yang kita tahu ada
echo "\nURL untuk file-file yang ada:\n";
echo "Kampanye Donasi: " . url('/storage/campaigns/InSSJOcl8VExPCIrBehsVFQrXYgRzyOhTeDKqogC.jpg') . "\n";
echo "Kampanye Relawan: " . url('/storage/volunteer_campaigns/1765265338_kominfo1706.jpg') . "\n";