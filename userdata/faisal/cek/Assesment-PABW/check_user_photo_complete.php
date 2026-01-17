<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Storage;

// Ambil user dengan ID 3 (reza)
$user = User::find(3);

if ($user) {
    echo "User ID: {$user->id}\n";
    echo "User Name: {$user->name}\n";
    echo "Current Photo Value: " . ($user->photo ?? 'NULL') . "\n";
    
    if ($user->photo) {
        // Cek apakah file fisik ada di storage
        $storagePath = 'public/' . $user->photo;
        $fullPath = storage_path('app/' . $storagePath);
        
        echo "Storage Path: {$storagePath}\n";
        echo "Full Path: {$fullPath}\n";
        echo "File exists in storage: " . (Storage::exists($storagePath) ? 'YES' : 'NO') . "\n";
        echo "File exists (direct check): " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        
        if (file_exists($fullPath)) {
            echo "File size: " . filesize($fullPath) . " bytes\n";
            echo "File type: " . mime_content_type($fullPath) . "\n";
        }
        
        // Cek URL yang dihasilkan
        $url = asset('storage/' . $user->photo);
        echo "Generated URL: {$url}\n";
    } else {
        echo "User doesn't have a photo set.\n";
    }
} else {
    echo "User with ID 3 not found.\n";
}

// Cek semua file di direktori profile-photos
echo "\nFiles in profile-photos directory:\n";
$profilePhotosPath = storage_path('app/public/profile-photos');
if (is_dir($profilePhotosPath)) {
    $files = scandir($profilePhotosPath);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - {$file}\n";
            
            // Cek apakah file ini direferensikan oleh user
            $referenced = User::where('photo', 'LIKE', "%{$file}%")->first();
            if ($referenced) {
                echo "    -> Referenced by user ID: {$referenced->id}, Name: {$referenced->name}\n";
            } else {
                echo "    -> NOT REFERENCED by any user\n";
            }
        }
    }
} else {
    echo "profile-photos directory doesn't exist: {$profilePhotosPath}\n";
}