<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first call_server_id
$callServerId = App\Models\CallServer::first()->id ?? 1;

echo "Creating softphone extensions 6011-6020...\n";

for ($i = 6011; $i <= 6020; $i++) {
    $existing = App\Models\Extension::where('extension', $i)->first();
    if ($existing) {
        echo "Extension $i already exists, skipping\n";
        continue;
    }

    App\Models\Extension::create([
        'call_server_id' => $callServerId,
        'extension' => (string) $i,
        'name' => "Softphone $i",
        'type' => 'softphone',
        'secret' => bin2hex(random_bytes(8)),
        'context' => 'internal',
        'is_active' => true,
    ]);
    echo "Created extension $i (softphone)\n";
}

echo "\nDone! Softphone extensions created.\n";

// Verify
$count = App\Models\Extension::where('type', 'softphone')->count();
echo "Total softphone extensions: $count\n";
