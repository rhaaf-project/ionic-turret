<?php
/**
 * Fix: Add sbc_id to topology data using line-by-line replacement
 */

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$lines = file($file);

if (!$lines) {
    die("ERROR: Cannot read file\n");
}

// Check if already fixed
$content = implode('', $lines);
if (strpos($content, "'sbc_id' =>") !== false) {
    echo "✅ File already contains sbc_id - no changes needed\n";
    exit(0);
}

$newLines = [];
$branchCount = 0;

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];

    // Detect '$nodes[] = [' for branch node (inside foreach)
    // Need to check context - look for 'has_sbc' a few lines down
    if (preg_match('/\$nodes\[\] = \[/', $line)) {
        // Look ahead to find 'has_sbc' line
        $hassbcLineIndex = null;
        for ($j = $i + 1; $j < min($i + 10, count($lines)); $j++) {
            if (strpos($lines[$j], "'has_sbc' =>") !== false && strpos($lines[$j], "->exists()") !== false) {
                $hassbcLineIndex = $j;
                break;
            }
        }

        if ($hassbcLineIndex !== null) {
            $branchCount++;

            // Add variable assignment BEFORE $nodes[]
            $varName = $branchCount == 1 ? '$branchSbc' : '$orphanSbc';
            $indent = '                '; // Match existing indentation
            $newLines[] = "{$indent}// Get SBC for this branch\n";
            $newLines[] = "{$indent}{$varName} = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();\n";
            $newLines[] = "\n";

            // Add current line ($nodes[] = [)
            $newLines[] = $line;

            // Continue adding until has_sbc line, then replace it
            for ($k = $i + 1; $k < count($lines); $k++) {
                if ($k == $hassbcLineIndex) {
                    // Replace has_sbc line with two lines
                    $newLines[] = "                    'has_sbc' => {$varName} !== null,\n";
                    $newLines[] = "                    'sbc_id' => {$varName}?->id,\n";
                    $i = $k; // Move main loop pointer
                    break;
                } else {
                    $newLines[] = $lines[$k];
                }
            }
            continue;
        }
    }

    $newLines[] = $line;
}

// Backup
file_put_contents($file . '.bak3', implode('', $lines));
echo "✅ Backup created: {$file}.bak3\n";

// Write fixed content
$newContent = implode('', $newLines);
if (file_put_contents($file, $newContent)) {
    echo "✅ Fixed: Added sbc_id to {$branchCount} node(s)\n";

    if (strpos($newContent, "'sbc_id' =>") !== false) {
        echo "✅ Verified: sbc_id is in the file\n";
    }
} else {
    die("ERROR: Cannot write file\n");
}

echo "\nDone! Run: php artisan optimize:clear\n";