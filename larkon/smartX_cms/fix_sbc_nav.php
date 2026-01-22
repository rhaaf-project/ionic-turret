<?php
// Fix SBC click - add SBC navigation option when clicking node with SBC

$file = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$content = file_get_contents($file);

// Find the existing click handler and replace with enhanced version
$oldHandler = <<<'JS'
            // Click handler - navigate to edit page
            network.on('click', function (params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    if (nodeId.startsWith('ho_')) {
                        const id = nodeId.replace('ho_', '');
                        window.location.href = '/admin/head-offices/' + id + '/edit';
                    } else if (nodeId.startsWith('br_')) {
                        const id = nodeId.replace('br_', '');
                        window.location.href = '/admin/branches/' + id + '/edit';
                    }
                }
JS;

$newHandler = <<<'JS'
            // Click handler - navigate to edit page, with SBC option
            network.on('click', function (params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    const node = topologyData.nodes.find(n => n.id === nodeId);
                    
                    if (nodeId.startsWith('ho_')) {
                        const id = nodeId.replace('ho_', '');
                        window.location.href = '/admin/head-offices/' + id + '/edit';
                    } else if (nodeId.startsWith('br_')) {
                        const id = nodeId.replace('br_', '');
                        // If node has SBC, show options
                        if (node && node.sbc_id) {
                            if (confirm('This branch has an SBC.\n\nClick OK to view SBC details.\nClick Cancel to view Branch details.')) {
                                window.location.href = '/admin/sbcs/' + node.sbc_id + '/edit';
                            } else {
                                window.location.href = '/admin/branches/' + id + '/edit';
                            }
                        } else {
                            window.location.href = '/admin/branches/' + id + '/edit';
                        }
                    }
                }
JS;

$content = str_replace($oldHandler, $newHandler, $content);
file_put_contents($file, $content);
echo "Updated click handler with SBC navigation option\n";

echo "\nDone!\n";
