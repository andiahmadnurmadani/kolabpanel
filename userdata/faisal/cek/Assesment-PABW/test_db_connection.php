<?php
require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';

// Create a kernel to handle the request
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Bootstrap the application
$app->make(Illuminate\Contracts\Http\Kernel::class);

// Get database configuration
$config = $app['config']['database'];
echo "Default Connection: " . $config['default'] . "\n";
echo "MySQL Host: " . ($config['connections']['mysql']['host'] ?? 'Not set') . "\n";
echo "MySQL Database: " . ($config['connections']['mysql']['database'] ?? 'Not set') . "\n";

// Try to get current connection
try {
    $db = $app['db'];
    $connection = $db->connection();
    echo "Current driver: " . $connection->getDriverName() . "\n";
    echo "Connection name: " . $connection->getName() . "\n";
} catch (Exception $e) {
    echo "Connection error: " . $e->getMessage() . "\n";
}