<?php
// fix_line_table_final.php
// Direct line-by-line removal of Type, Channels, Trunk columns

$file = '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);
$newLines = [];
$skipLines = 0;

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];

    // Skip remaining lines if we're in a multi-line block to remove
    if ($skipLines > 0) {
        $skipLines--;
        continue;
    }

    // Skip Type column (3 lines)
    if (strpos($line, "TextColumn::make('type')") !== false) {
        $skipLines = 2; // Skip next 2 lines
        continue;
    }

    // Skip channel_count column (2 lines)
    if (strpos($line, "TextColumn::make('channel_count')") !== false) {
        $skipLines = 1; // Skip next 1 line
        continue;
    }

    // Skip trunk.name column (3 lines)
    if (strpos($line, "TextColumn::make('trunk.name')") !== false) {
        $skipLines = 2; // Skip next 2 lines
        continue;
    }

    // Skip Type filter (2 lines)
    if (strpos($line, "SelectFilter::make('type')") !== false) {
        $skipLines = 1;
        continue;
    }

    $newLines[] = $line;
}

$content = implode("\n", $newLines);
file_put_contents($file, $content);
echo "Removed Type, Channels, Trunk columns from LineItemResource\n";

// Verify
$content = file_get_contents($file);
$hasType = strpos($content, "make('type')") !== false;
$hasChannel = strpos($content, "make('channel_count')") !== false;
$hasTrunk = strpos($content, "make('trunk.name')") !== false;

echo "Verification:\n";
echo "  Type column: " . ($hasType ? "STILL EXISTS (FAIL)" : "REMOVED (OK)") . "\n";
echo "  Channel column: " . ($hasChannel ? "STILL EXISTS (FAIL)" : "REMOVED (OK)") . "\n";
echo "  Trunk column: " . ($hasTrunk ? "STILL EXISTS (FAIL)" : "REMOVED (OK)") . "\n";
echo "Done!\n";
