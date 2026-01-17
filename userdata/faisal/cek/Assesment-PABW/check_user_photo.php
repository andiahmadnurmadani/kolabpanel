<?php
require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Get user with ID 3 (based on previous error showing user ID 3)
$user = User::find(3);

if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "User Name: " . $user->name . "\n";
    echo "Photo Field Value: " . ($user->photo ?? 'NULL') . "\n";
    
    if ($user->photo) {
        $fullPath = 'storage/' . $user->photo;
        $absolutePath = $app->storagePath('app/public/' . $user->photo);
        echo "Expected file path: " . $absolutePath . "\n";
        echo "File exists: " . (file_exists($absolutePath) ? 'YES' : 'NO') . "\n";
        
        // Check if it's a valid path
        $pathInfo = pathinfo($user->photo);
        echo "File extension: " . ($pathInfo['extension'] ?? 'none') . "\n";
        echo "File basename: " . $pathInfo['basename'] . "\n";
    }
} else {
    echo "User with ID 3 not found in database.\n";
}

// Also check all users with photos
echo "\nAll users with photos:\n";
$usersWithPhotos = User::whereNotNull('photo')->get();
foreach ($usersWithPhotos as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Photo: {$user->photo}\n";
}