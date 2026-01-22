<?php
// Check extension types and migrate with case-insensitive match

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Extension;
use App\Models\ThirdPartyDevice;
use Illuminate\Support\Facades\DB;

// Check what types exist
$types = DB::table('extensions')->select('type')->distinct()->pluck('type');
echo "Extension types in database:\n";
foreach ($types as $t) {
    $count = Extension::where('type', $t)->count();
    echo "- '$t' ($count records)\n";
}

// Find all softphone extensions (case insensitive)
$softphoneExtensions = Extension::whereRaw('LOWER(type) = ?', ['softphone'])->get();
echo "\nFound " . $softphoneExtensions->count() . " Softphone extensions to migrate\n";

foreach ($softphoneExtensions as $ext) {
    // Check if 3rd Party table exists and has required columns
    try {
        $thirdParty = ThirdPartyDevice::create([
            'call_server_id' => $ext->call_server_id,
            'extension' => $ext->extension,
            'name' => $ext->name,
            'secret' => $ext->secret ?? '',
            'description' => $ext->description ?? null,
            'type' => 'softphone',
        ]);

        echo "Migrated: {$ext->extension} - {$ext->name}\n";

        // Delete from extensions table
        $ext->delete();
        echo "  Deleted from Extensions\n";
    } catch (Exception $e) {
        echo "Error migrating {$ext->extension}: " . $e->getMessage() . "\n";
    }
}

echo "\nDone!\n";
