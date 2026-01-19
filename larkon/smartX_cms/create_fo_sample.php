<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create HO Bandung Basic
$ho = App\Models\HeadOffice::create([
    'customer_id' => 1,
    'name' => 'HO Bandung Basic',
    'type' => 'basic',
    'province' => 'Jawa Barat',
    'city' => 'Bandung',
    'is_active' => true,
]);

echo "Created HO: " . $ho->name . " (ID: " . $ho->id . ")\n";

// Create 1 Call Server for Basic
$cs = App\Models\CallServer::create([
    'head_office_id' => $ho->id,
    'name' => 'SmartUCX-Solo',
    'host' => '10.0.2.1',
    'port' => 5060,
    'is_active' => true,
]);
echo "Created Server: " . $cs->name . " -> " . $ho->name . "\n";

echo "Done!\n";
