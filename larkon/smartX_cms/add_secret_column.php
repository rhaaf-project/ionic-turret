<?php
// add_secret_column.php
// Add secret column to lines table

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::statement('ALTER TABLE lines ADD COLUMN secret VARCHAR(255) NULL');
    echo "Added 'secret' column to lines table\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'duplicate') !== false) {
        echo "Column 'secret' already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
