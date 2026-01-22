<?php
// Copy exact navigation line from VpwResource to ExtensionResource and ThirdPartyDeviceResource

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// Get the exact navigation line from VpwResource
$vpwContent = file_get_contents("$basePath/VpwResource.php");

// Find the navigation lines
preg_match("/protected static \?string \\\$navigationParentItem = '[^']+';/", $vpwContent, $matches);
$navParentLine = $matches[0] ?? null;
echo "VpwResource navigationParentItem: $navParentLine\n";

preg_match("/protected static \?int \\\$navigationSort = \d+;/", $vpwContent, $matches);
$navSortLine = $matches[0] ?? null;
echo "VpwResource navigationSort: $navSortLine\n";

// Now update ExtensionResource
$extFile = "$basePath/ExtensionResource.php";
$extContent = file_get_contents($extFile);

// Check what currently in ExtensionResource
preg_match("/protected static \?string \\\$navigationParentItem = '[^']+';/", $extContent, $matches);
echo "ExtensionResource current: " . ($matches[0] ?? 'NOT FOUND') . "\n";

// If navigationParentItem doesn't exist but navigationGroup does, replace it
if (strpos($extContent, '$navigationGroup') !== false) {
    // Replace navigationGroup with navigationParentItem from VPW
    $extContent = preg_replace(
        "/protected static \?string \\\$navigationGroup = '[^']+';/",
        "protected static ?string \$navigationParentItem = 'Line ▾';",
        $extContent
    );
    file_put_contents($extFile, $extContent);
    echo "Replaced navigationGroup with navigationParentItem in ExtensionResource\n";
}

// Do the same for ThirdPartyDeviceResource
$thirdFile = "$basePath/ThirdPartyDeviceResource.php";
$thirdContent = file_get_contents($thirdFile);

if (strpos($thirdContent, '$navigationGroup') !== false) {
    $thirdContent = preg_replace(
        "/protected static \?string \\\$navigationGroup = '[^']+';/",
        "protected static ?string \$navigationParentItem = 'Line ▾';",
        $thirdContent
    );
    file_put_contents($thirdFile, $thirdContent);
    echo "Replaced navigationGroup with navigationParentItem in ThirdPartyDeviceResource\n";
}

echo "\nDone!\n";
