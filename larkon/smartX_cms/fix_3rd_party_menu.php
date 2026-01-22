<?php
// 1. Move 3rd Party under Line submenu
// 2. Remove Type column from Extension list

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// 1. Update ThirdPartyDeviceResource - move under Line submenu
$file = "$basePath/ThirdPartyDeviceResource.php";
$content = file_get_contents($file);

// Change navigation parent and sort
$content = str_replace(
    "protected static ?string \$navigationGroup = 'Connectivity';",
    "protected static ?string \$navigationParentItem = 'Line';",
    $content
);
$content = str_replace(
    "protected static ?int \$navigationSort = 6;",
    "protected static ?int \$navigationSort = 99;",
    $content
);

file_put_contents($file, $content);
echo "Updated ThirdPartyDeviceResource - now under Line submenu\n";

// 2. Update ExtensionResource - remove Type column from table
$extFile = "$basePath/ExtensionResource.php";
if (file_exists($extFile)) {
    $extContent = file_get_contents($extFile);

    // Remove Type column from table
    $patterns = [
        "/Tables\\\\Columns\\\\TextColumn::make\('type'\)[^,]*,\s*/s",
        "/Tables\\\\Columns\\\\BadgeColumn::make\('type'\)[^,]*,\s*/s",
    ];

    foreach ($patterns as $pattern) {
        $extContent = preg_replace($pattern, '', $extContent);
    }

    file_put_contents($extFile, $extContent);
    echo "Updated ExtensionResource - removed Type column\n";
}

echo "\nDone!\n";
