<?php
// Fix InterconnectivityResource - proper sbc_id implementation

$resourceFile = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($resourceFile);

// First, let's restore the file and do a proper fix
// Find the branch loop and add sbc lookup before the array

$pattern = "/foreach \(\\\$ho->branches as \\\$branch\) \{/";
$replacement = "foreach (\$ho->branches as \$branch) {\n                \$branchSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();";

$content = preg_replace($pattern, $replacement, $content);

// Replace has_sbc line with proper reference
$content = str_replace(
    "\\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();\n                    'has_sbc' => \$sbc !== null,\n                    'sbc_id' => \$sbc?->id,",
    "'has_sbc' => \$branchSbc !== null,\n                    'sbc_id' => \$branchSbc?->id,",
    $content
);

// Also fix any remaining old pattern
$content = str_replace(
    "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),",
    "'has_sbc' => \$branchSbc !== null,\n                    'sbc_id' => \$branchSbc?->id,",
    $content
);

// Fix orphan branches loop
$orphanPattern = "/foreach \(\\\$orphanBranches as \\\$branch\) \{/";
$orphanReplacement = "foreach (\$orphanBranches as \$branch) {\n            \$branchSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();";

$content = preg_replace($orphanPattern, $orphanReplacement, $content);

// Fix orphan branch has_sbc
$content = str_replace(
    "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),\n                'sbc_id'",
    "'has_sbc' => \$branchSbc !== null,\n                'sbc_id' => \$branchSbc?->id,\n                'sbc_id_old'",
    $content
);

file_put_contents($resourceFile, $content);
echo "Fixed InterconnectivityResource with proper sbc_id\n";

echo "\nDone!\n";
