<?php
/**
 * Update topology-map.blade.php to use custom PNG icons
 * - head-office.png for HeadOffice nodes
 * - branch.png for Branch nodes without SBC
 * - branch-sbc.png for Branch nodes with SBC
 */

$file = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$content = file_get_contents($file);

if (!$content) {
    die("ERROR: Cannot read file\n");
}

// Backup
file_put_contents($file . '.icons_bak', $content);
echo "✅ Backup created\n";

// Replace the icon URL declarations
$oldIcons = <<<'JS'
// Icon URLs - using icons8 for reliable PNGs
            const cloudIcon = 'https://img.icons8.com/fluency/96/cloud.png';
            const serverIcon = 'https://img.icons8.com/fluency/96/server.png';
JS;

$newIcons = <<<'JS'
// Custom icon URLs - local images
            const headOfficeIcon = '/images/topology/head-office.png';
            const branchIcon = '/images/topology/branch.png';
            const branchSbcIcon = '/images/topology/branch-sbc.png';
JS;

$content = str_replace($oldIcons, $newIcons, $content);

// Update headoffice node to use headOfficeIcon
$oldHO = "image: cloudIcon,";
$newHO = "image: headOfficeIcon,";
$content = str_replace($oldHO, $newHO, $content);

// Update branch/standalone nodes to use conditional icon based on has_sbc
// For standalone nodes
$oldStandalone = <<<'JS'
} else if (node.group === 'standalone') {
                    branchCount++;
                    return {
                        id: node.id,
                        label: node.label + '\ntype: standalone\nIP: ' + node.ip + (node.has_sbc ? '\n🔴 SBC' : ''),
                        title: node.title,
                        shape: 'image',
                        image: serverIcon,
JS;

$newStandalone = <<<'JS'
} else if (node.group === 'standalone') {
                    branchCount++;
                    return {
                        id: node.id,
                        label: node.label + '\ntype: standalone\nIP: ' + node.ip,
                        title: node.title,
                        shape: 'image',
                        image: node.has_sbc ? branchSbcIcon : branchIcon,
JS;

$content = str_replace($oldStandalone, $newStandalone, $content);

// For regular branch nodes  
$oldBranch = <<<'JS'
} else {
                    branchCount++;
                    const isActive = node.status === 'OK';
                    return {
                        id: node.id,
                        label: node.label + '\ntype: peer\nIP: ' + node.ip + '\n' + (isActive ? 'OK' : 'Offline') + (node.has_sbc ? '\n🔴 SBC' : ''),
                        title: node.title,
                        shape: 'image',
                        image: serverIcon,
JS;

$newBranch = <<<'JS'
} else {
                    branchCount++;
                    const isActive = node.status === 'OK';
                    return {
                        id: node.id,
                        label: node.label + '\ntype: peer\nIP: ' + node.ip + '\n' + (isActive ? 'OK' : 'Offline'),
                        title: node.title,
                        shape: 'image',
                        image: node.has_sbc ? branchSbcIcon : branchIcon,
JS;

$content = str_replace($oldBranch, $newBranch, $content);

// Write updated content
if (file_put_contents($file, $content)) {
    echo "✅ Updated topology-map.blade.php with custom icons\n";
    echo "\nIcons used:\n";
    echo "  - Head Office: /images/topology/head-office.png\n";
    echo "  - Branch: /images/topology/branch.png\n";
    echo "  - Branch with SBC: /images/topology/branch-sbc.png\n";
} else {
    die("ERROR: Cannot write file\n");
}

echo "\nDone! Refresh the topology page to see new icons.\n";
