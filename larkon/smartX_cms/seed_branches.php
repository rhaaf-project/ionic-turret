<?php
// Clean and create proper sample data - 1 HO Jakarta with 3 branches
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HeadOffice;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CallServer;

echo "Cleaning up sample data...\n";

// Delete all branches first
Branch::query()->delete();
echo "Deleted all branches\n";

// Delete all HO except keep one Jakarta
HeadOffice::where('name', 'NOT LIKE', '%Jakarta%')->orWhere('name', 'LIKE', '%Jakarta HA%')->delete();
echo "Deleted extra head offices\n";

// Keep/Create HO Jakarta
$customer = Customer::first();
if (!$customer) {
    $customer = Customer::create(['name' => 'PT Smart Telecom', 'code' => 'SMART']);
    echo "Created customer: PT Smart Telecom\n";
}

$hoJakarta = HeadOffice::firstOrCreate(
    ['name' => 'HO Jakarta'],
    ['customer_id' => $customer->id, 'type' => 'ha', 'is_active' => true]
);
echo "HO Jakarta ready (ID: {$hoJakarta->id})\n";

// Create Call Servers for branches
$callServers = [
    ['name' => 'Bandung PBX', 'host' => '10.1.0.10'],
    ['name' => 'Surabaya PBX', 'host' => '10.1.0.20'],
    ['name' => 'Medan PBX', 'host' => '10.1.0.30'],
];

$csIds = [];
foreach ($callServers as $cs) {
    $server = CallServer::firstOrCreate(
        ['host' => $cs['host']],
        ['name' => $cs['name'], 'port' => 5060, 'is_active' => true]
    );
    $csIds[$cs['name']] = $server->id;
    echo "Call Server: {$cs['name']} ({$cs['host']})\n";
}

// Create branches with real IPs
$branches = [
    ['name' => 'Bandung', 'city' => 'Bandung', 'call_server' => 'Bandung PBX', 'is_active' => true],
    ['name' => 'Surabaya', 'city' => 'Surabaya', 'call_server' => 'Surabaya PBX', 'is_active' => true],
    ['name' => 'Medan', 'city' => 'Medan', 'call_server' => 'Medan PBX', 'is_active' => true],
];

foreach ($branches as $branchData) {
    $branch = Branch::create([
        'name' => $branchData['name'],
        'head_office_id' => $hoJakarta->id,
        'customer_id' => $customer->id,
        'city' => $branchData['city'],
        'call_server_id' => $csIds[$branchData['call_server']],
        'is_active' => $branchData['is_active'],
    ]);
    $status = $branchData['is_active'] ? 'OK' : 'Offline';
    echo "  Branch: {$branchData['name']} -> {$branchData['call_server']} ({$status})\n";
}

echo "\n=== FINAL DATA ===\n";
echo "Head Offices: " . HeadOffice::count() . "\n";
echo "Branches: " . Branch::count() . "\n";
echo "Call Servers: " . CallServer::count() . "\n";
