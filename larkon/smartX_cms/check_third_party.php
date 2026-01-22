<?php
// check_third_party.php
// Check if third_party_devices table exists and has correct structure

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Third Party Devices Table ===\n";
if (Schema::hasTable('third_party_devices')) {
    $columns = Schema::getColumnListing('third_party_devices');
    print_r($columns);

    $count = DB::table('third_party_devices')->count();
    echo "\nTotal records: $count\n";
} else {
    echo "Table 'third_party_devices' does NOT exist!\n";
    echo "Creating table...\n";

    DB::statement("
        CREATE TABLE third_party_devices (
            id SERIAL PRIMARY KEY,
            call_server_id INTEGER REFERENCES call_servers(id),
            extension VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            secret VARCHAR(255),
            description TEXT,
            type VARCHAR(50) DEFAULT 'softphone',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table created!\n";
}

echo "\nDone!\n";
