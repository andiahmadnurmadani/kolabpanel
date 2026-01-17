<?php
require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Check all users that might have photos
$users = User::whereNotNull('photo')->get();

echo "Users with photos in database:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Photo: {$user->photo}\n";
    
    // Check if file exists in storage
    $storagePath = storage_path('app/public/' . $user->photo);
    $urlPath = $app->storagePath('app/public/' . $user->photo);
    
    echo "  Storage Path: {$storagePath}\n";
    echo "  File exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
    
    // Check if public path exists (through symlink)
    $publicPath = public_path('storage/' . $user->photo);
    echo "  Public Path: {$publicPath}\n";
    echo "  Public file exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
    
    // Generate URL
    $url = asset('storage/' . $user->photo);
    echo "  Generated URL: {$url}\n";
    echo "\n";
}

// Also check files in the profile-photos directory
$profilePhotosDir = storage_path('app/public/profile-photos');
if (is_dir($profilePhotosDir)) {
    echo "Files in profile-photos directory:\n";
    $files = scandir($profilePhotosDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - {$file}\n";
            
            // Check if this file is referenced in any user record
            $userWithPhoto = User::where('photo', 'profile-photos/' . $file)->first();
            if ($userWithPhoto) {
                echo "    Referenced by user ID: {$userWithPhoto->id}, Name: {$userWithPhoto->name}\n";
            } else {
                echo "    Not referenced by any user\n";
            }
        }
    }
} else {
    echo "profile-photos directory does not exist: {$profilePhotosDir}\n";
}