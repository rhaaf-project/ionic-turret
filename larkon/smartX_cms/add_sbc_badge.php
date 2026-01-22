<?php
// Add SBC badge to topology nodes

$file = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$content = file_get_contents($file);

// Add SBC legend to the legend section
$oldLegend = '<span class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-red-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Branch (Offline)</span>
                    </span>
                </div>';

$newLegend = '<span class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-red-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Branch (Offline)</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-bold">SBC</span>
                        <span class="text-gray-600 dark:text-gray-400">Has SBC</span>
                    </span>
                </div>';

$content = str_replace($oldLegend, $newLegend, $content);

// Update node label to include SBC indicator if has_sbc = true
// Modify the JavaScript to show SBC in label
$oldLabel = "label: node.label + '\\ntype: peer\\nIP: ' + node.ip + '\\n' + (isActive ? 'OK' : 'Offline'),";
$newLabel = "label: node.label + '\\ntype: peer\\nIP: ' + node.ip + '\\n' + (isActive ? 'OK' : 'Offline') + (node.has_sbc ? '\\n🔴 SBC' : ''),";

$content = str_replace($oldLabel, $newLabel, $content);

// Also update standalone nodes
$oldStandalone = "label: node.label + '\\ntype: standalone\\nIP: ' + node.ip,";
$newStandalone = "label: node.label + '\\ntype: standalone\\nIP: ' + node.ip + (node.has_sbc ? '\\n🔴 SBC' : ''),";

$content = str_replace($oldStandalone, $newStandalone, $content);

file_put_contents($file, $content);
echo "Updated topology-map.blade.php with SBC indicator\n";

// Now update InterconnectivityResource to include has_sbc in node data
$resourceFile = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
if (file_exists($resourceFile)) {
    $resourceContent = file_get_contents($resourceFile);

    // Add has_sbc to node data - check if SBC exists for this branch
    // This assumes there's a relationship or we need to check SBC table
    echo "Note: Need to add has_sbc field to topology data in InterconnectivityResource\n";
}

echo "Done! Please update InterconnectivityResource::getTopologyData() to include 'has_sbc' => true/false for each node.\n";
