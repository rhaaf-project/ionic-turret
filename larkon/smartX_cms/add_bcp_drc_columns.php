<?php
/**
 * Add missing bcp_drc columns to head_offices table
 * 
 * Run via: ssh root@103.154.80.171 "cd /var/www/html/smartX && php add_bcp_drc_columns.php"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== Adding BCP/DRC columns to head_offices ===\n\n";

Schema::table('head_offices', function (Blueprint $table) {
    // Add bcp_drc_server_id column if not exists
    if (!Schema::hasColumn('head_offices', 'bcp_drc_server_id')) {
        $table->foreignId('bcp_drc_server_id')->nullable()->constrained('call_servers')->nullOnDelete();
        echo "✓ Added bcp_drc_server_id column\n";
    } else {
        echo "! bcp_drc_server_id already exists\n";
    }

    // Add bcp_drc_enabled column if not exists
    if (!Schema::hasColumn('head_offices', 'bcp_drc_enabled')) {
        $table->boolean('bcp_drc_enabled')->default(false);
        echo "✓ Added bcp_drc_enabled column\n";
    } else {
        echo "! bcp_drc_enabled already exists\n";
    }
});

echo "\n=== Done ===\n";
