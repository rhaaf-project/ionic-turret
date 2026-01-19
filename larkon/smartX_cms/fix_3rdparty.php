<?php
$file = '/var/www/html/smartX/app/Filament/Resources/ThirdPartyDeviceResource.php';
$content = file_get_contents($file);
$content = str_replace(
    "protected static ?string \$navigationGroup = 'Connectivity';",
    "protected static ?string \$navigationGroup = 'Connectivity';\n    protected static ?string \$navigationParentItem = 'Line ▾';",
    $content
);
file_put_contents($file, $content);
echo "Fixed ThirdPartyDeviceResource!\n";
