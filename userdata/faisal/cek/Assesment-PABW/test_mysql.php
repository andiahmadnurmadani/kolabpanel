<?php
// Test MySQL connection specifically
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    // Test MySQL connection specifically
    $mysqlConnection = DB::connection('mysql');
    $driver = $mysqlConnection->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "MySQL connection driver: " . $driver . "\n";
    
    // Check if we can run a simple query
    $result = $mysqlConnection->select('SELECT 1 as test');
    echo "MySQL connection successful: " . json_encode($result) . "\n";
} catch (Exception $e) {
    echo "MySQL connection failed: " . $e->getMessage() . "\n";
}