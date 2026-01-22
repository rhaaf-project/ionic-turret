<?php
/**
 * Script to create Recording Server table
 * 
 * Run via: ssh root@103.154.80.171 "cd /var/www/html/smartX && php seed_recording_server.php"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "=== Recording Server Setup ===\n\n";

// Create recording_servers table
if (!Schema::hasTable('recording_servers')) {
    echo "Creating recording_servers table...\n";
    Schema::create('recording_servers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('call_server_id')->nullable()->constrained('call_servers')->nullOnDelete();
        $table->string('ip_address');
        $table->string('port')->nullable();
        $table->string('pbx_system_type')->default('None');
        $table->string('pbx_system_1')->nullable();
        $table->string('pbx_system_2')->nullable();
        $table->string('pbx_system_3')->nullable();
        $table->string('pbx_system_4')->nullable();
        $table->string('description')->nullable();
        $table->boolean('is_enabled')->default(true);
        $table->timestamps();
    });
    echo "✓ Table created\n";
} else {
    echo "✓ Table already exists\n";
}

echo "\n=== Done ===\n";
echo "Recording Server feature is ready!\n";
echo "Navigate to: CMS -> Recording -> Rec Server\n";
