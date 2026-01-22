<?php
// fix_vpw_cas_fields.php
// Check database columns and fix form field mapping

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VPW Table Columns ===\n";
$vpwColumns = Schema::getColumnListing('vpws');
print_r($vpwColumns);

echo "\n=== CAS Table Columns ===\n";
$casColumns = Schema::getColumnListing('cas');
print_r($casColumns);

echo "\n=== VPW Model Fillable ===\n";
$vpwModel = new \App\Models\Vpw();
print_r($vpwModel->getFillable());

echo "\n=== CAS Model Fillable ===\n";
$casModel = new \App\Models\Cas();
print_r($casModel->getFillable());

echo "\nDone!\n";
