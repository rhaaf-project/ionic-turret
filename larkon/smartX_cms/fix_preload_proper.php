<?php
// fix_preload_proper.php
// Add ->preload() ONLY to Form Select components (call_server_id), NOT to Table Columns

$files = [
    'app/Filament/Resources/LineItemResource.php',
    'app/Filament/Resources/ExtensionResource.php',
    'app/Filament/Resources/VpwResource.php',
    'app/Filament/Resources/CasResource.php',
    'app/Filament/Resources/ThirdPartyDeviceResource.php',
];

foreach ($files as $file) {
    $path = '/var/www/html/smartX/' . $file;
    if (!file_exists($path)) {
        echo "SKIP: $file not found\n";
        continue;
    }

    $content = file_get_contents($path);

    // Only add preload to Form Select for call_server_id
    // Look for: Select::make('call_server_id') ... ->searchable(),
    // Replace with: ... ->searchable()->preload(),

    // Skip if already has preload after Select
    if (strpos($content, "->searchable()\n                    ->preload()") !== false) {
        echo "SKIP (already has preload): $file\n";
        continue;
    }

    // Target: Forms\Components\Select::make('call_server_id') block ending with ->searchable(),
    $pattern = "/(Forms\\\\Components\\\\Select::make\\('call_server_id'\\)[^;]+->searchable\\(\\)),/";

    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, "$1\n                    ->preload(),", $content);
        file_put_contents($path, $content);
        echo "FIXED preload: $file\n";
    } else {
        echo "PATTERN NOT FOUND: $file\n";
    }
}

echo "\nDone!\n";
