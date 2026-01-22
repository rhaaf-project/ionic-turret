<?php
// Direct fix for InterconnectivityResource - add sbc_id

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($file);

// Replace the has_sbc line with proper sbc lookup + sbc_id
// Pattern 1: for branches under head office
$search1 = "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),
                ];";

$replace1 = "\$branchSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();
                'has_sbc' => \$branchSbc !== null,
                'sbc_id' => \$branchSbc?->id,
                ];";

// First, let's re-read the actual content pattern
$lines = explode("\n", $content);
$newLines = [];
$i = 0;
while ($i < count($lines)) {
    $line = $lines[$i];

    // Check if this is the has_sbc line for branches
    if (strpos($line, "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists()") !== false) {
        // Add the sbc lookup before and sbc_id after
        $indent = str_repeat(' ', strlen($line) - strlen(ltrim($line)));
        $newLines[] = $indent . "\$branchSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();";
        $newLines[] = $indent . "'has_sbc' => \$branchSbc !== null,";
        $newLines[] = $indent . "'sbc_id' => \$branchSbc?->id,";
    } else {
        $newLines[] = $line;
    }
    $i++;
}

$newContent = implode("\n", $newLines);
file_put_contents($file, $newContent);
echo "Fixed InterconnectivityResource with sbc_id\n";

// Verify fix
$content = file_get_contents($file);
if (strpos($content, 'sbc_id') !== false) {
    echo "Verified: sbc_id is now in the file\n";
} else {
    echo "WARNING: sbc_id still not found!\n";
}

echo "\nDone!\n";
