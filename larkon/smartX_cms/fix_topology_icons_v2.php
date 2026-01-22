<?php
// fix_topology_icons_v2.php
// Complete fix for topology icons

$file = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$content = file_get_contents($file);

// Replace icon constant definitions
$content = str_replace(
    "// Icon URLs - using icons8 for reliable PNGs\n            const cloudIcon = 'https://img.icons8.com/fluency/96/cloud.png';\n            const serverIcon = 'https://img.icons8.com/fluency/96/server.png';",
    "// Icon URLs - local PNG files\n            const headOfficeIcon = '/images/topology/head-office.png';\n            const branchIcon = '/images/topology/branch.png';\n            const branchSbcIcon = '/images/topology/branch-sbc.png';",
    $content
);

// Replace standalone branch image logic
$content = str_replace(
    "image: serverIcon,\n                        size: 30,\n                        font: { color: '#374151', size: 10, face: 'arial', multi: true }\n                    };\n                } else {",
    "image: node.has_sbc ? branchSbcIcon : branchIcon,\n                        size: 40,\n                        font: { color: '#374151', size: 10, face: 'arial', multi: true }\n                    };\n                } else {",
    $content
);

// Replace regular branch image logic
$content = str_replace(
    "image: serverIcon,\n                        size: 30,\n                        font: { color: isActive ? '#16a34a' : '#dc2626', size: 10, face: 'arial', multi: true }\n                    };\n                }",
    "image: node.has_sbc ? branchSbcIcon : branchIcon,\n                        size: 40,\n                        font: { color: isActive ? '#16a34a' : '#dc2626', size: 10, face: 'arial', multi: true }\n                    };\n                }",
    $content
);

// Remove emoji from labels since we now use icons
$content = str_replace("(node.has_sbc ? '\\n🔴 SBC' : '')", "''", $content);

file_put_contents($file, $content);

// Verify
$newContent = file_get_contents($file);
echo "Verification:\n";
echo "  headOfficeIcon defined: " . (strpos($newContent, "const headOfficeIcon") !== false ? "YES" : "NO") . "\n";
echo "  branchIcon defined: " . (strpos($newContent, "const branchIcon") !== false ? "YES" : "NO") . "\n";
echo "  branchSbcIcon defined: " . (strpos($newContent, "const branchSbcIcon") !== false ? "YES" : "NO") . "\n";
echo "  serverIcon removed: " . (strpos($newContent, "serverIcon") === false ? "YES" : "NO") . "\n";
echo "  uses branchSbcIcon: " . (strpos($newContent, "branchSbcIcon") !== false ? "YES" : "NO") . "\n";
echo "\nDone!\n";
