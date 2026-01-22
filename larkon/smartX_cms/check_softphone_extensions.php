<?php
// check_softphone_extensions.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Extensions with type='softphone' ===\n";
$softphones = DB::table('extensions')->where('type', 'softphone')->get();
echo "Count: " . $softphones->count() . "\n";
foreach ($softphones as $ext) {
    echo "  ID: {$ext->id}, Extension: {$ext->extension}, Name: {$ext->name}, Type: {$ext->type}\n";
}

echo "\n=== All Extensions (first 20) ===\n";
$all = DB::table('extensions')->limit(20)->get();
foreach ($all as $ext) {
    echo "  ID: {$ext->id}, Ext: {$ext->extension}, Name: {$ext->name}, Type: {$ext->type}\n";
}

echo "\nDone!\n";
