<?php
/**
 * Restore all files to original state before edits
 */

echo "=== RESTORING ALL FILES ===\n\n";

$restorations = [
    // InterconnectivityResource - restore from .bak3 (original before sbc_id fix)
    '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php.bak'
    => '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php',

    // LineItemResource - restore from .dropdown_bak (before preload fix) 
    '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php.dropdown_bak'
    => '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php',

    // ExtensionResource
    '/var/www/html/smartX/app/Filament/Resources/ExtensionResource.php.dropdown_bak'
    => '/var/www/html/smartX/app/Filament/Resources/ExtensionResource.php',

    // VpwResource
    '/var/www/html/smartX/app/Filament/Resources/VpwResource.php.dropdown_bak'
    => '/var/www/html/smartX/app/Filament/Resources/VpwResource.php',

    // CasResource
    '/var/www/html/smartX/app/Filament/Resources/CasResource.php.dropdown_bak'
    => '/var/www/html/smartX/app/Filament/Resources/CasResource.php',

    // ThirdPartyDeviceResource
    '/var/www/html/smartX/app/Filament/Resources/ThirdPartyDeviceResource.php.dropdown_bak'
    => '/var/www/html/smartX/app/Filament/Resources/ThirdPartyDeviceResource.php',

    // topology-map.blade.php
    '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php.icons_bak'
    => '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php',
];

$restored = 0;
$notFound = 0;

foreach ($restorations as $backup => $target) {
    if (file_exists($backup)) {
        copy($backup, $target);
        echo "✅ Restored: " . basename($target) . "\n";
        $restored++;
    } else {
        echo "⚠️  No backup: " . basename($backup) . "\n";
        $notFound++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Restored: $restored files\n";
echo "No backup found: $notFound files\n";

echo "\nClearing cache...\n";
passthru('php artisan optimize:clear');

echo "\n✅ DONE! All files restored to original state.\n";
