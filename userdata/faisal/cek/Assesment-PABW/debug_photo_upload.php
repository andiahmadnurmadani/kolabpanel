<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Storage;

// Ambil user yang sedang login (atau user pertama untuk testing)
$user = User::find(3); // Gunakan user ID 3 seperti sebelumnya

if (!$user) {
    $user = User::first(); // Ambil user pertama jika user ID 3 tidak ditemun
}

if ($user) {
    echo "User ID: {$user->id}\n";
    echo "User Name: {$user->name}\n";
    echo "Photo field value: " . ($user->photo ?? 'NULL') . "\n";
    
    if ($user->photo) {
        echo "Photo path: {$user->photo}\n";
        
        // Cek apakah file fisik ada
        $fullPath = storage_path('app/public/' . $user->photo);
        $publicPath = public_path('storage/' . $user->photo);
        
        echo "Storage path: {$fullPath}\n";
        echo "File exists in storage: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        
        echo "Public path: {$publicPath}\n";
        echo "File exists in public: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
        
        // Cek URL yang dihasilkan
        $url = asset('storage/' . $user->photo);
        echo "Generated URL: {$url}\n";
        
        // Cek apakah symlink storage ada
        $symlinkPath = public_path('storage');
        echo "Storage symlink exists: " . (file_exists($symlinkPath) ? 'YES' : 'NO') . "\n";
        if (file_exists($symlinkPath)) {
            echo "Symlink target: " . readlink($symlinkPath) . "\n";
        }
    } else {
        echo "User doesn't have a photo set.\n";
    }
} else {
    echo "No user found in database.\n";
}

// Juga cek file-file di direktori profile-photos
$profilePhotosDir = storage_path('app/public/profile-photos');
echo "\nChecking profile-photos directory:\n";
if (is_dir($profilePhotosDir)) {
    $files = scandir($profilePhotosDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - {$file}\n";
            
            // Cek apakah file ini direferensikan oleh user manapun
            $referenced = User::where('photo', 'LIKE', "%{$file}%")->first();
            if ($referenced) {
                echo "    Referenced by user ID: {$referenced->id}, Name: {$referenced->name}\n";
            } else {
                echo "    NOT REFERENCED by any user\n";
            }
        }
    }
} else {
    echo "profile-photos directory doesn't exist: {$profilePhotosDir}\n";
}