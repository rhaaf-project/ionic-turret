<?php
// Fix navigation - use navigationGroup instead of navigationParentItem

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// 1. Fix ExtensionResource
$extFile = "$basePath/ExtensionResource.php";
$content = file_get_contents($extFile);

// Remove navigationParentItem and add navigationGroup
$content = str_replace(
    "protected static ?string \$navigationParentItem = 'Line ▾';",
    "protected static ?string \$navigationGroup = 'Connectivity';",
    $content
);

file_put_contents($extFile, $content);
echo "Fixed ExtensionResource\n";

// 2. Fix ThirdPartyDeviceResource  
$thirdFile = "$basePath/ThirdPartyDeviceResource.php";
$content = file_get_contents($thirdFile);

$content = str_replace(
    "protected static ?string \$navigationParentItem = 'Line ▾';",
    "protected static ?string \$navigationGroup = 'Connectivity';",
    $content
);

file_put_contents($thirdFile, $content);
echo "Fixed ThirdPartyDeviceResource\n";

echo "\nDone!\n";
