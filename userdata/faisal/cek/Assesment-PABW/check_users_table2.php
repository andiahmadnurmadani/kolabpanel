<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Get table structure
$columns = DB::select('SHOW COLUMNS FROM users');
echo "Columns in users table:\n";
foreach ($columns as $column) {
    echo "- {$column->Field} (Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key})\n";
}