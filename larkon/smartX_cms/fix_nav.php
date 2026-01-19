<?php
// Quick fix for ThirdPartyDevice and Intercom navigation
require __DIR__ . '/vendor/autoload.php';

$fixes = [
    '/var/www/html/smartX/app/Filament/Resources/ThirdPartyDeviceResource.php' => [
        'from' => "protected static ?string \$navigationGroup = 'Device';",
        'to' => "protected static ?string \$navigationGroup = 'Connectivity';\n    protected static ?string \$navigationParentItem = 'Line ▾';"
    ],
    '/var/www/html/smartX/app/Filament/Resources/IntercomResource.php' => [
        'from' => "// protected static ?string \$navigationParentItem = 'Line';",
        'to' => ""
    ],
];

foreach ($fixes as $file => $fix) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $newContent = str_replace($fix['from'], $fix['to'], $content);
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Fixed: $file\n";
        } else {
            echo "No change: $file\n";
        }
    } else {
        echo "Not found: $file\n";
    }
}

// Fix IntercomResource sort to 7
$intercomFile = '/var/www/html/smartX/app/Filament/Resources/IntercomResource.php';
$content = file_get_contents($intercomFile);
$content = preg_replace('/navigationSort = \d+/', 'navigationSort = 7', $content);
file_put_contents($intercomFile, $content);
echo "Updated Intercom sort to 7\n";

echo "Done!\n";
