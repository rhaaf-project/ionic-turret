<?php
/**
 * Script to create Static Route table and seed sample data
 * Sample data from user's spreadsheet:
 * - Network: 13.44.13.0, Subnet: 255.255.255.0, Gateway: 13.44.13.2, Device: Eth1
 * - Network: 10.80.8.0, Subnet: 255.255.255.0, Gateway: 13.44.13.2, Device: Eth1
 * 
 * Run via: ssh root@103.154.80.171 "cd /var/www/html/smartX && php seed_static_routes.php"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "=== Static Routes Setup ===\n\n";

// Step 1: Create table if not exists
if (!Schema::hasTable('static_routes')) {
    echo "Creating static_routes table...\n";
    Schema::create('static_routes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('call_server_id')->nullable()->constrained('call_servers')->nullOnDelete();
        $table->string('network');
        $table->string('subnet');
        $table->string('gateway');
        $table->string('device');
        $table->string('description')->nullable();
        $table->timestamps();
    });
    echo "✓ Table created\n";
} else {
    echo "✓ Table already exists\n";
}

// Step 2: Get first call server (SmartUCX-1) for associating routes
$callServer = DB::table('call_servers')->first();
$callServerId = $callServer ? $callServer->id : null;

if ($callServer) {
    echo "✓ Found call server: {$callServer->name} (ID: {$callServerId})\n";
} else {
    echo "! No call server found, routes will be unassigned\n";
}

// Step 3: Seed sample data
$sampleRoutes = [
    [
        'call_server_id' => $callServerId,
        'network' => '13.44.13.0',
        'subnet' => '255.255.255.0',
        'gateway' => '13.44.13.2',
        'device' => 'Eth1',
        'description' => 'Internal network route',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'call_server_id' => $callServerId,
        'network' => '10.80.8.0',
        'subnet' => '255.255.255.0',
        'gateway' => '13.44.13.2',
        'device' => 'Eth1',
        'description' => 'Secondary network route',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Clear existing sample data (optional)
$existingCount = DB::table('static_routes')->count();
if ($existingCount > 0) {
    echo "! Found {$existingCount} existing routes\n";
    echo "Clear existing routes? (Delete to re-seed) - Keeping existing data\n";
} else {
    echo "Inserting sample routes...\n";
    foreach ($sampleRoutes as $route) {
        DB::table('static_routes')->insert($route);
        echo "  + Added route: {$route['network']} -> {$route['gateway']} via {$route['device']}\n";
    }
    echo "✓ Sample data inserted\n";
}

// Step 4: Display final count
$finalCount = DB::table('static_routes')->count();
echo "\n=== Done ===\n";
echo "Total static routes: {$finalCount}\n";
