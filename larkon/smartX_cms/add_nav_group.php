<?php
// Add navigationGroup to ExtensionResource and ThirdPartyDeviceResource

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// 1. Fix ExtensionResource - add navigationGroup before navigationParentItem
$extFile = "$basePath/ExtensionResource.php";
$extContent = file_get_contents($extFile);

// Check if navigationGroup is missing
if (strpos($extContent, '$navigationGroup') === false) {
    // Add navigationGroup before navigationParentItem
    $extContent = str_replace(
        "protected static ?string \$navigationParentItem = 'Line ▾';",
        "protected static ?string \$navigationGroup = 'Connectivity';\n    \n    protected static ?string \$navigationParentItem = 'Line ▾';",
        $extContent
    );
    file_put_contents($extFile, $extContent);
    echo "Added navigationGroup = 'Connectivity' to ExtensionResource\n";
} else {
    echo "ExtensionResource already has navigationGroup\n";
}

// 2. Fix ThirdPartyDeviceResource
$thirdFile = "$basePath/ThirdPartyDeviceResource.php";
$thirdContent = file_get_contents($thirdFile);

if (strpos($thirdContent, '$navigationGroup') === false) {
    $thirdContent = str_replace(
        "protected static ?string \$navigationParentItem = 'Line ▾';",
        "protected static ?string \$navigationGroup = 'Connectivity';\n    \n    protected static ?string \$navigationParentItem = 'Line ▾';",
        $thirdContent
    );
    file_put_contents($thirdFile, $thirdContent);
    echo "Added navigationGroup = 'Connectivity' to ThirdPartyDeviceResource\n";
} else {
    echo "ThirdPartyDeviceResource already has navigationGroup\n";
}

echo "\nDone! Both resources now have navigationGroup + navigationParentItem\n";
