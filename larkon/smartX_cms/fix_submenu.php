<?php
// Fix Extension and 3rd Party to be under Line submenu

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// 1. Fix ExtensionResource - use Line ▾ as parent and sort after Line (2)
$extFile = "$basePath/ExtensionResource.php";
$content = file_get_contents($extFile);

// Replace navigationGroup with navigationParentItem
$content = str_replace(
    "protected static ?string \$navigationGroup = 'Connectivity';",
    "protected static ?string \$navigationParentItem = 'Line ▾';",
    $content
);

// Update sort to 2 (after Line item which is 1)
$content = str_replace(
    "protected static ?int \$navigationSort = 2;",
    "protected static ?int \$navigationSort = 2;",
    $content
);

file_put_contents($extFile, $content);
echo "Fixed ExtensionResource - now under Line ▾, sort=2\n";

// 2. Fix ThirdPartyDeviceResource - under Line, last position (sort 5)
$thirdFile = "$basePath/ThirdPartyDeviceResource.php";
$content = file_get_contents($thirdFile);

// Replace navigationGroup with navigationParentItem
$content = str_replace(
    "protected static ?string \$navigationGroup = 'Connectivity';",
    "protected static ?string \$navigationParentItem = 'Line ▾';",
    $content
);

// Keep sort as 99 to be last
file_put_contents($thirdFile, $content);
echo "Fixed ThirdPartyDeviceResource - now under Line ▾, sort=99\n";

echo "\nDone!\n";
