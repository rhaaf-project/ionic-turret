<?php
/**
 * Script to create Firewall table and seed sample data
 * Also adds more Static Route sample data for SmartUCX-2
 * 
 * Run via: ssh root@103.154.80.171 "cd /var/www/html/smartX && php seed_firewall.php"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "=== Firewall & Static Route Setup ===\n\n";

// ========================================
// STEP 1: Create firewalls table
// ========================================
if (!Schema::hasTable('firewalls')) {
    echo "Creating firewalls table...\n";
    Schema::create('firewalls', function (Blueprint $table) {
        $table->id();
        $table->foreignId('call_server_id')->nullable()->constrained('call_servers')->nullOnDelete();
        $table->string('port');
        $table->string('source')->default('any');
        $table->string('destination')->default('any');
        $table->string('protocol');
        $table->string('interface');
        $table->string('direction');
        $table->string('action');
        $table->string('comment')->nullable();
        $table->boolean('is_enabled')->default(true);
        $table->timestamps();
    });
    echo "✓ Firewalls table created\n";
} else {
    echo "✓ Firewalls table already exists\n";
}

// ========================================
// STEP 2: Get Call Servers
// ========================================
$callServers = DB::table('call_servers')->get();
$smartUCX1 = $callServers->first();
$smartUCX2 = $callServers->skip(1)->first();

echo "\n=== Call Servers ===\n";
if ($smartUCX1) {
    echo "SmartUCX-1: {$smartUCX1->name} (ID: {$smartUCX1->id})\n";
}
if ($smartUCX2) {
    echo "SmartUCX-2: {$smartUCX2->name} (ID: {$smartUCX2->id})\n";
}

// ========================================
// STEP 3: Seed Firewall data
// ========================================
echo "\n=== Seeding Firewall Rules ===\n";

$firewallCount = DB::table('firewalls')->count();
if ($firewallCount == 0) {
    $firewallRules = [];

    // SmartUCX-1 rules
    if ($smartUCX1) {
        $firewallRules[] = [
            'call_server_id' => $smartUCX1->id,
            'port' => '8760',
            'source' => 'any',
            'destination' => 'any',
            'protocol' => 'UDP, TCP',
            'interface' => 'eth1, eth2',
            'direction' => 'both',
            'action' => 'block',
            'comment' => 'test aja',
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $firewallRules[] = [
            'call_server_id' => $smartUCX1->id,
            'port' => '5060',
            'source' => '10.33.42.80',
            'destination' => 'any',
            'protocol' => 'UDP, TCP',
            'interface' => 'eth1, eth2',
            'direction' => 'both',
            'action' => 'block',
            'comment' => 'test aja',
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // SmartUCX-2 rules
    if ($smartUCX2) {
        $firewallRules[] = [
            'call_server_id' => $smartUCX2->id,
            'port' => '8760',
            'source' => 'any',
            'destination' => 'any',
            'protocol' => 'UDP, TCP',
            'interface' => 'eth1, eth2',
            'direction' => 'both',
            'action' => 'block',
            'comment' => 'test aja',
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    foreach ($firewallRules as $rule) {
        DB::table('firewalls')->insert($rule);
        echo "  + Added: Port {$rule['port']}, Source {$rule['source']}, Action {$rule['action']}\n";
    }
    echo "✓ Firewall rules inserted: " . count($firewallRules) . "\n";
} else {
    echo "! Skipped - {$firewallCount} rules already exist\n";
}

// ========================================
// STEP 4: Add Static Routes for SmartUCX-2
// ========================================
echo "\n=== Adding Static Routes for SmartUCX-2 ===\n";

if ($smartUCX2) {
    // Check if routes already exist for SmartUCX-2
    $existingRoutes = DB::table('static_routes')
        ->where('call_server_id', $smartUCX2->id)
        ->count();

    if ($existingRoutes == 0) {
        $routes = [
            [
                'call_server_id' => $smartUCX2->id,
                'network' => '13.44.13.0',
                'subnet' => '255.255.255.0',
                'gateway' => '13.44.13.2',
                'device' => 'Eth1',
                'description' => 'SmartUCX-2 internal route',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'call_server_id' => $smartUCX2->id,
                'network' => '10.80.8.0',
                'subnet' => '255.255.255.0',
                'gateway' => '13.44.13.2',
                'device' => 'Eth1',
                'description' => 'SmartUCX-2 secondary route',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($routes as $route) {
            DB::table('static_routes')->insert($route);
            echo "  + Added: {$route['network']} -> {$route['gateway']} via {$route['device']}\n";
        }
        echo "✓ Static routes for SmartUCX-2 inserted\n";
    } else {
        echo "! Skipped - {$existingRoutes} routes already exist for SmartUCX-2\n";
    }
} else {
    echo "! No SmartUCX-2 call server found\n";
}

// ========================================
// STEP 5: Summary
// ========================================
echo "\n=== Summary ===\n";
echo "Total Firewall Rules: " . DB::table('firewalls')->count() . "\n";
echo "Total Static Routes: " . DB::table('static_routes')->count() . "\n";
echo "\n=== Done ===\n";
