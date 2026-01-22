<?php
// Move Softphone extensions to 3rd Party table

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Extension;
use App\Models\ThirdPartyDevice;

// Find all softphone extensions
$softphoneExtensions = Extension::where('type', 'Softphone')->get();
echo "Found " . $softphoneExtensions->count() . " Softphone extensions to migrate\n";

foreach ($softphoneExtensions as $ext) {
    // Create 3rd Party record
    $thirdParty = ThirdPartyDevice::create([
        'call_server_id' => $ext->call_server_id,
        'extension' => $ext->extension,
        'name' => $ext->name,
        'secret' => $ext->secret,
        'description' => $ext->description ?? null,
        'type' => 'softphone',
    ]);

    echo "Migrated: {$ext->extension} - {$ext->name}\n";

    // Delete from extensions table
    $ext->delete();
    echo "  Deleted from Extensions\n";
}

echo "\nDone! Migrated " . $softphoneExtensions->count() . " records to 3rd Party.\n";
