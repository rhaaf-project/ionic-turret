#!/bin/bash
# Sync extension 6024 to Asterisk

cd /var/www/html/smartX

php artisan tinker --execute='
$ext = App\Models\Extension::where("extension","6024")->first();
if ($ext) {
    echo "Found: " . $ext->extension . " type:" . $ext->type . "\n";
    $result = app(App\Services\AsteriskService::class)->addExtension($ext->extension, $ext->secret, $ext->name, $ext->type);
    echo $result ? "Sync OK" : "Sync FAILED";
} else {
    echo "Extension 6024 not found!";
}
'
