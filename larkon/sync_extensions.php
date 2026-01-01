<?php
// Sync all active extensions from DB to Asterisk
// Run: php sync_extensions.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Extension;
use App\Services\AsteriskService;

echo "=== Syncing Extensions to Asterisk ===\n\n";

$asterisk = new AsteriskService();
$extensions = Extension::where('is_active', true)->get();

echo "Found " . $extensions->count() . " active extensions in DB\n\n";

foreach ($extensions as $ext) {
    echo "Syncing: {$ext->extension} ({$ext->name})... ";

    try {
        $result = $asterisk->addExtension($ext->extension, $ext->secret, $ext->name);
        echo $result ? "OK\n" : "FAILED\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Done! ===\n";
echo "Run: ssh root@103.154.80.172 docker exec asterisk grep '^\[' /etc/asterisk/pjsip_wizard.conf\n";
