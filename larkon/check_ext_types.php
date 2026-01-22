<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$extensions = App\Models\Extension::where('extension', '>=', '6000')
    ->where('extension', '<=', '6020')
    ->select('extension', 'name', 'type')
    ->get();

echo "Extensions 6000-6020:\n";
echo str_pad('Extension', 12) . str_pad('Name', 30) . "Type\n";
echo str_repeat('-', 60) . "\n";

foreach ($extensions as $ext) {
    echo str_pad($ext->extension, 12) . str_pad($ext->name ?? '-', 30) . ($ext->type ?? 'null') . "\n";
}
