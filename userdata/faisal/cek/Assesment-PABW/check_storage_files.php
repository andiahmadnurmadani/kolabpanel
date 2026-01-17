<?php
// File untuk memeriksa file-file di folder volunteer_campaigns

require_once __DIR__.'/vendor/autoload.php';

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob(storage_path('app/public/volunteer_campaigns/*'));
echo "Files in volunteer_campaigns folder:\n";
foreach ($files as $file) {
    echo basename($file) . "\n";
}

echo "\nChecking if files exist in campaigns folder:\n";
$files = glob(storage_path('app/public/campaigns/*'));
foreach ($files as $file) {
    echo basename($file) . "\n";
}