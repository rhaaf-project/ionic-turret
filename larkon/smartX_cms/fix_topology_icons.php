<?php
// fix_topology_icons.php
// Update topology-map.blade.php to use local PNG icons

$file = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$content = file_get_contents($file);

// Replace external icon URLs with local paths
$oldIcons = "// Icon URLs - using icons8 for reliable PNGs
            const cloudIcon = 'https://img.icons8.com/fluency/96/cloud.png';
            const serverIcon = 'https://img.icons8.com/fluency/96/server.png';";

$newIcons = "// Icon URLs - local PNG files
            const headOfficeIcon = '/images/topology/head-office.png';
            const branchIcon = '/images/topology/branch.png';
            const branchSbcIcon = '/images/topology/branch-sbc.png';";

$content = str_replace($oldIcons, $newIcons, $content);

// Update head office node to use headOfficeIcon
$content = str_replace(
    "image: cloudIcon,",
    "image: headOfficeIcon,",
    $content
);

// Update branch nodes to use branchIcon or branchSbcIcon based on has_sbc
// Find and replace the branch node logic (standalone)
$oldStandalone = "} else if (node.group === 'standalone') {
                    branchCount++;
                    return {
                        id: node.id,
                        label: node.label + '\\ntype: standalone\\nIP: ' + node.ip + (node.has_sbc ? '\\n🔴 SBC' : ''),
                        title: node.title,
                        shape: 'image',
                        image: serverIcon,
                        size: 30,
                        font: { color: '#374151', size: 10, face: 'arial', multi: true }
                    };
                }";

$newStandalone = "} else if (node.group === 'standalone') {
                    branchCount++;
                    return {
                        id: node.id,
                        label: node.label + '\\ntype: standalone\\nIP: ' + node.ip,
                        title: node.title,
                        shape: 'image',
                        image: node.has_sbc ? branchSbcIcon : branchIcon,
                        size: 40,
                        font: { color: '#374151', size: 10, face: 'arial', multi: true }
                    };
                }";

$content = str_replace($oldStandalone, $newStandalone, $content);

// Update regular branch node logic
$oldBranch = "} else {
                    branchCount++;
                    const isActive = node.status === 'OK';
                    return {
                        id: node.id,
                        label: node.label + '\\ntype: peer\\nIP: ' + node.ip + '\\n' + (isActive ? 'OK' : 'Offline') + (node.has_sbc ? '\\n🔴 SBC' : ''),
                        title: node.title,
                        shape: 'image',
                        image: serverIcon,
                        size: 30,
                        font: { color: isActive ? '#16a34a' : '#dc2626', size: 10, face: 'arial', multi: true }
                    };
                }";

$newBranch = "} else {
                    branchCount++;
                    const isActive = node.status === 'OK';
                    return {
                        id: node.id,
                        label: node.label + '\\ntype: peer\\nIP: ' + node.ip + '\\n' + (isActive ? 'OK' : 'Offline'),
                        title: node.title,
                        shape: 'image',
                        image: node.has_sbc ? branchSbcIcon : branchIcon,
                        size: 40,
                        font: { color: isActive ? '#16a34a' : '#dc2626', size: 10, face: 'arial', multi: true }
                    };
                }";

$content = str_replace($oldBranch, $newBranch, $content);

file_put_contents($file, $content);

// Verify
$newContent = file_get_contents($file);
$hasNewIcons = strpos($newContent, 'branchSbcIcon') !== false;
$hasHeadOffice = strpos($newContent, 'headOfficeIcon') !== false;

echo "Updated topology-map.blade.php to use local PNG icons\n";
echo "Verification:\n";
echo "  - headOfficeIcon: " . ($hasHeadOffice ? "YES" : "NO") . "\n";
echo "  - branchSbcIcon: " . ($hasNewIcons ? "YES" : "NO") . "\n";
echo "Done!\n";
