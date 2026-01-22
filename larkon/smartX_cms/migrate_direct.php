<?php
// Migrate softphone using direct DB insert

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check what tables exist for 3rd party
$tables = DB::select("SHOW TABLES LIKE '%third%'");
echo "Tables with 'third':\n";
foreach ($tables as $t) {
    $arr = (array) $t;
    echo "- " . array_values($arr)[0] . "\n";
}

$tables = DB::select("SHOW TABLES LIKE '%device%'");
echo "\nTables with 'device':\n";
foreach ($tables as $t) {
    $arr = (array) $t;
    echo "- " . array_values($arr)[0] . "\n";
}

$tables = DB::select("SHOW TABLES LIKE '%party%'");
echo "\nTables with 'party':\n";
foreach ($tables as $t) {
    $arr = (array) $t;
    echo "- " . array_values($arr)[0] . "\n";
}

// Get softphone extensions
$softphones = DB::table('extensions')->whereRaw('LOWER(type) = ?', ['softphone'])->get();
echo "\nFound " . count($softphones) . " softphone extensions\n";

// Try to insert into third_party_devices table
$targetTable = 'third_party_devices';
if (Schema::hasTable($targetTable)) {
    echo "\nMigrating to $targetTable...\n";

    foreach ($softphones as $ext) {
        DB::table($targetTable)->insert([
            'call_server_id' => $ext->call_server_id,
            'extension' => $ext->extension,
            'name' => $ext->name,
            'secret' => $ext->secret ?? '',
            'description' => $ext->description ?? null,
            'type' => 'softphone',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Migrated: {$ext->extension} - {$ext->name}\n";

        // Delete from extensions
        DB::table('extensions')->where('id', $ext->id)->delete();
    }
    echo "\nDone!\n";
} else {
    echo "\nTable $targetTable not found. Available tables checked above.\n";
}
