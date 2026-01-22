<?php
// Add sbc_id to topology data and make SBC clickable

// 1. Update InterconnectivityResource to include sbc_id
$resourceFile = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($resourceFile);

// Add sbc_id to branch nodes
$content = str_replace(
    "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),",
    "\$sbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();\n                    'has_sbc' => \$sbc !== null,\n                    'sbc_id' => \$sbc?->id,",
    $content
);

// Fix for standalone branches too
$content = str_replace(
    "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),\n                'ip'",
    "\$sbc = \\App\\Models\\Sbc::where('branch_id', \$branch->id)->first();\n                'has_sbc' => \$sbc !== null,\n                'sbc_id' => \$sbc?->id,\n                'ip'",
    $content
);

file_put_contents($resourceFile, $content);
echo "Updated InterconnectivityResource with sbc_id\n";

// 2. Update topology-map.blade.php to add click handler
$bladeFile = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$bladeContent = file_get_contents($bladeFile);

// Check if click handler already exists
if (strpos($bladeContent, 'network.on') === false) {
    // Find where to add click handler - after network is created
    $insertAfter = "const network = new vis.Network(container, { nodes, edges }, options);";

    $clickHandler = "
            // Handle node click for SBC navigation
            network.on('click', function(params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    const node = topologyData.nodes.find(n => n.id === nodeId);
                    if (node && node.sbc_id) {
                        // Navigate to SBC detail page
                        window.location.href = '/admin/sbcs/' + node.sbc_id + '/edit';
                    }
                }
            });
            
            // Change cursor on hover over nodes with SBC
            network.on('hoverNode', function(params) {
                const node = topologyData.nodes.find(n => n.id === params.node);
                if (node && node.sbc_id) {
                    container.style.cursor = 'pointer';
                }
            });
            
            network.on('blurNode', function() {
                container.style.cursor = 'default';
            });";

    $bladeContent = str_replace($insertAfter, $insertAfter . $clickHandler, $bladeContent);
    file_put_contents($bladeFile, $bladeContent);
    echo "Added click handler to topology-map.blade.php\n";
} else {
    echo "Click handler already exists\n";
}

echo "\nDone!\n";
