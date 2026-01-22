<?php
// fix_dropdown_and_table.php
// 1. Add ->preload() to Call Server dropdown on Line, Extension, VPW, CAS, ThirdPartyDevice
// 2. Remove Type column from Line table

$files = [
    'app/Filament/Resources/LineResource.php',
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

    // Add ->preload() after ->searchable() if not already present
    if (strpos($content, '->preload()') === false) {
        $content = str_replace(
            "->searchable(),",
            "->searchable()\n                    ->preload(),",
            $content
        );
        echo "FIXED preload: $file\n";
    } else {
        echo "SKIP preload (already has): $file\n";
    }

    file_put_contents($path, $content);
}

// Remove Type column from Line table
$lineFile = '/var/www/html/smartX/app/Filament/Resources/LineResource.php';
$content = file_get_contents($lineFile);

// Remove the Type badge column
$typeColumnPattern = "/Tables\\\\Columns\\\\TextColumn::make\\('type'\\)[^;]+;/s";
if (preg_match($typeColumnPattern, $content)) {
    $content = preg_replace($typeColumnPattern, "// Type column removed - Line is always webrtc", $content);
    file_put_contents($lineFile, $content);
    echo "FIXED: Removed Type column from Line table\n";
} else {
    // Try simpler approach - look for the badge line
    $oldType = "Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('warning'),";
    $newType = "// Type column removed - always webrtc";

    if (strpos($content, "TextColumn::make('type')") !== false) {
        // Use multi-line removal
        $lines = explode("\n", $content);
        $newLines = [];
        $skipUntilComma = false;

        foreach ($lines as $line) {
            if (strpos($line, "TextColumn::make('type')") !== false) {
                $skipUntilComma = true;
                $newLines[] = "                // Type column removed - always webrtc";
                continue;
            }
            if ($skipUntilComma) {
                if (strpos($line, ',') !== false && strpos($line, '->') !== false) {
                    $skipUntilComma = false;
                }
                continue;
            }
            $newLines[] = $line;
        }

        $content = implode("\n", $newLines);
        file_put_contents($lineFile, $content);
        echo "FIXED: Removed Type column from Line table (method 2)\n";
    }
}

echo "\nDone!\n";
