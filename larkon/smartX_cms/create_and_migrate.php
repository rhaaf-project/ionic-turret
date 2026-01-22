<?php
// Create third_party_devices table and migrate softphone extensions

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Create third_party_devices table if not exists
if (!Schema::hasTable('third_party_devices')) {
    Schema::create('third_party_devices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('call_server_id')->nullable()->constrained('call_servers');
        $table->string('extension');
        $table->string('name');
        $table->string('secret')->nullable();
        $table->text('description')->nullable();
        $table->string('type')->default('softphone');
        $table->timestamps();
    });
    echo "Created third_party_devices table\n";
} else {
    echo "Table third_party_devices already exists\n";
}

// Get softphone extensions
$softphones = DB::table('extensions')->whereRaw('LOWER(type) = ?', ['softphone'])->get();
echo "\nFound " . count($softphones) . " softphone extensions to migrate\n";

foreach ($softphones as $ext) {
    DB::table('third_party_devices')->insert([
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
