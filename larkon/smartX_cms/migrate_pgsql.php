<?php
// Migrate softphone using direct DB insert - PostgreSQL compatible

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// PostgreSQL: List tables
$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
echo "Tables with 'third' or 'party' or 'device':\n";
foreach ($tables as $t) {
    if (
        stripos($t->tablename, 'third') !== false ||
        stripos($t->tablename, 'party') !== false ||
        stripos($t->tablename, 'device') !== false
    ) {
        echo "- " . $t->tablename . "\n";
    }
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
    echo "\nDone! All softphone extensions moved to 3rd Party.\n";
} else {
    echo "\nTable '$targetTable' not found. Checking if we need to create it...\n";

    // List all tables
    echo "All available tables:\n";
    foreach ($tables as $t) {
        echo "- " . $t->tablename . "\n";
    }
}
