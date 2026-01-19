<?php
// Seed sample SBC and Private Wire data
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sbc;
use App\Models\PrivateWire;
use App\Models\CallServer;

echo "Creating sample SBC and Private Wire data...\n";

// Get first call server
$callServer = CallServer::first();
if (!$callServer) {
    echo "No Call Server found, creating one...\n";
    $callServer = CallServer::create([
        'name' => 'Main PBX',
        'host' => '103.154.80.171',
        'port' => 5060,
        'is_active' => true,
    ]);
}

// Create sample SBCs
$sbcs = [
    [
        'name' => 'SBC_Telkom_Main',
        'sip_server' => '202.134.1.10',
        'sip_server_port' => 5060,
        'transport' => 'udp',
        'context' => 'from-pstn',
        'codecs' => 'ulaw,alaw',
        'dtmfmode' => 'auto',
        'outcid' => '02150877001',
        'maxchans' => 30,
        'disabled' => false,
    ],
    [
        'name' => 'SBC_Indosat_Backup',
        'sip_server' => '202.155.30.5',
        'sip_server_port' => 5060,
        'transport' => 'tcp',
        'context' => 'from-pstn',
        'codecs' => 'alaw,ulaw',
        'dtmfmode' => 'rfc4733',
        'outcid' => '02150877002',
        'maxchans' => 10,
        'disabled' => false,
    ],
];

foreach ($sbcs as $sbcData) {
    $sbc = Sbc::firstOrCreate(
        ['name' => $sbcData['name']],
        array_merge($sbcData, ['call_server_id' => $callServer->id])
    );
    $status = $sbc->disabled ? 'Disabled' : 'Active';
    echo "  SBC: {$sbc->name} -> {$sbc->sip_server} ({$status})\n";
}

// Create sample Private Wires
$privateWires = [
    [
        'name' => 'PW-Jakarta-Bandung',
        'number' => '9001',
        'destination' => '10.1.0.10',
        'description' => 'Private wire ke kantor Bandung',
        'is_active' => true,
    ],
    [
        'name' => 'PW-Jakarta-Surabaya',
        'number' => '9002',
        'destination' => '10.1.0.20',
        'description' => 'Private wire ke kantor Surabaya',
        'is_active' => true,
    ],
    [
        'name' => 'PW-Jakarta-Medan',
        'number' => '9003',
        'destination' => '10.1.0.30',
        'description' => 'Private wire ke kantor Medan',
        'is_active' => true,
    ],
    [
        'name' => 'PW-Jakarta-Makassar',
        'number' => '9004',
        'destination' => '10.1.0.40',
        'description' => 'Private wire ke kantor Makassar - OFFLINE',
        'is_active' => false,
    ],
    [
        'name' => 'PW-Jakarta-Semarang',
        'number' => '9005',
        'destination' => '10.1.0.50',
        'description' => 'Private wire ke kantor Semarang',
        'is_active' => true,
    ],
];

foreach ($privateWires as $pwData) {
    $pw = PrivateWire::firstOrCreate(
        ['name' => $pwData['name']],
        array_merge($pwData, ['call_server_id' => $callServer->id])
    );
    $status = $pw->is_active ? 'In Use (green)' : 'Not In Use (red)';
    echo "  Private Wire: {$pw->name} ({$pw->number}) -> {$status}\n";
}

echo "\n=== SUMMARY ===\n";
echo "SBCs: " . Sbc::count() . "\n";
echo "Private Wires: " . PrivateWire::count() . "\n";
echo "  - In Use: " . PrivateWire::where('is_active', true)->count() . "\n";
echo "  - Not In Use: " . PrivateWire::where('is_active', false)->count() . "\n";
