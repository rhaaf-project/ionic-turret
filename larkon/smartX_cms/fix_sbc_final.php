<?php
// Properly fix InterconnectivityResource - put variable BEFORE array

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($file);

// Pattern 1: Add branchSbc before $nodes[] for branches under HO
$old1 = "\$nodes[] = [
                    'id' => \$nodeId,
                    'label' => \$branch->name,
                    'title' => \"{\$branch->name}\\nType: peer\\nIP: {\$ip}\\nStatus: {\$status}\",
                    'group' => 'branch',
                    'status' => \$status,
                    'ip' => \$ip,
                    'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),
                ];";

$new1 = "\$branchSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();
                \$nodes[] = [
                    'id' => \$nodeId,
                    'label' => \$branch->name,
                    'title' => \"{\$branch->name}\\nType: peer\\nIP: {\$ip}\\nStatus: {\$status}\",
                    'group' => 'branch',
                    'status' => \$status,
                    'ip' => \$ip,
                    'has_sbc' => \$branchSbc !== null,
                    'sbc_id' => \$branchSbc?->id,
                ];";

$content = str_replace($old1, $new1, $content);

// Pattern 2: Similarly for orphan branches  
$old2 = "\$nodes[] = [
                'id' => 'br_' . \$branch->id,
                'label' => \$branch->name,
                'title' => \"{\$branch->name}\\nType: standalone\\nIP: {\$ip}\",
                'group' => 'standalone',
                'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),
                'ip' => \$ip,
            ];";

$new2 = "\$orphanSbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();
            \$nodes[] = [
                'id' => 'br_' . \$branch->id,
                'label' => \$branch->name,
                'title' => \"{\$branch->name}\\nType: standalone\\nIP: {\$ip}\",
                'group' => 'standalone',
                'has_sbc' => \$orphanSbc !== null,
                'sbc_id' => \$orphanSbc?->id,
                'ip' => \$ip,
            ];";

$content = str_replace($old2, $new2, $content);

file_put_contents($file, $content);

// Verify syntax
exec('php -l ' . escapeshellarg($file) . ' 2>&1', $output, $return);
if ($return === 0) {
    echo "SUCCESS: File syntax is valid!\n";
    echo "sbc_id added to both branch types\n";
} else {
    echo "ERROR: Syntax error in file:\n";
    echo implode("\n", $output) . "\n";
}

echo "\nDone!\n";
