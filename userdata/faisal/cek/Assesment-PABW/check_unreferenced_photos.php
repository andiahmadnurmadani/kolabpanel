<?php
require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Get all photo files in profile-photos directory
$allPhotos = glob($app->storagePath('app/public/profile-photos/*'));
echo 'Total files in profile-photos: ' . count($allPhotos) . "\n";

$referencedPhotos = [];
$users = User::whereNotNull('photo')->get();
foreach($users as $user) {
    if(strpos($user->photo, 'profile-photos') !== false) {
        $referencedPhotos[] = $app->storagePath('app/public/' . $user->photo);
    }
}

echo 'Files referenced in database: ' . count($referencedPhotos) . "\n";

$unreferencedFiles = array_diff($allPhotos, $referencedPhotos);
echo 'Unreferenced files: ' . count($unreferencedFiles) . "\n";

foreach($unreferencedFiles as $file) {
    echo '  - ' . basename($file) . ' (size: ' . filesize($file) . ' bytes)' . "\n";
}

// Also show all users with photo values
echo "\nUsers with photo values in database:\n";
foreach($users as $user) {
    if(strpos($user->photo, 'profile-photos') !== false) {
        $exists = file_exists($app->storagePath('app/public/' . $user->photo));
        echo "  - User ID: {$user->id}, Name: {$user->name}, Photo: {$user->photo}, Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
}