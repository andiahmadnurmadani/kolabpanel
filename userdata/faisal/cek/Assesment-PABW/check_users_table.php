<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get table structure
$columns = DB::select('DESCRIBE users');
echo "Columns in users table:\n";
foreach ($columns as $column) {
    echo "- {$column->Field} (Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key})\n";
}