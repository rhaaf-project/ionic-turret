<?php
// Properly add sbc_id to InterconnectivityResource

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($file);

// Fix 1: For regular branches - add sbc lookup before array
$oldCode1 = <<<'PHP'
                $nodes[] = [
                    'id' => $nodeId,
                    'label' => $branch->name,
                    'title' => "{$branch->name}\nType: peer\nIP: {$ip}\nStatus: {$status}",
                    'group' => 'branch',
                    'status' => $status,
                    'ip' => $ip,
                    'has_sbc' => \App\Models\Sbc::where('branch_id', $branch->id)->exists(),
                ];
PHP;

$newCode1 = <<<'PHP'
                $branchSbc = \App\Models\Sbc::where('branch_id', $branch->id)->first();
                $nodes[] = [
                    'id' => $nodeId,
                    'label' => $branch->name,
                    'title' => "{$branch->name}\nType: peer\nIP: {$ip}\nStatus: {$status}",
                    'group' => 'branch',
                    'status' => $status,
                    'ip' => $ip,
                    'has_sbc' => $branchSbc !== null,
                    'sbc_id' => $branchSbc?->id,
                ];
PHP;

$content = str_replace($oldCode1, $newCode1, $content);

// Fix 2: For orphan branches
$oldCode2 = <<<'PHP'
            $nodes[] = [
                'id' => 'br_' . $branch->id,
                'label' => $branch->name,
                'title' => "{$branch->name}\nType: standalone\nIP: {$ip}",
                'group' => 'standalone',
                'has_sbc' => \App\Models\Sbc::where('branch_id', $branch->id)->exists(),
                'ip' => $ip,
            ];
PHP;

$newCode2 = <<<'PHP'
            $orphanSbc = \App\Models\Sbc::where('branch_id', $branch->id)->first();
            $nodes[] = [
                'id' => 'br_' . $branch->id,
                'label' => $branch->name,
                'title' => "{$branch->name}\nType: standalone\nIP: {$ip}",
                'group' => 'standalone',
                'has_sbc' => $orphanSbc !== null,
                'sbc_id' => $orphanSbc?->id,
                'ip' => $ip,
            ];
PHP;

$content = str_replace($oldCode2, $newCode2, $content);

file_put_contents($file, $content);
echo "Added sbc_id to InterconnectivityResource\n";

// 3. Add click handler to blade file (fix only if not already added properly)
$bladeFile = '/var/www/html/smartX/resources/views/filament/pages/topology-map.blade.php';
$blade = file_get_contents($bladeFile);

if (strpos($blade, "window.location.href = '/admin/sbcs/'") === false) {
    $insertAfter = "const network = new vis.Network(container, { nodes, edges }, options);";

    $clickHandler = "\n\n            // Handle SBC click - navigate to SBC detail page\n            network.on('click', function(params) {\n                if (params.nodes.length > 0) {\n                    const nodeId = params.nodes[0];\n                    const node = topologyData.nodes.find(n => n.id === nodeId);\n                    if (node && node.sbc_id) {\n                        window.location.href = '/admin/sbcs/' + node.sbc_id + '/edit';\n                    }\n                }\n            });";

    $blade = str_replace($insertAfter, $insertAfter . $clickHandler, $blade);
    file_put_contents($bladeFile, $blade);
    echo "Added click handler to topology-map.blade.php\n";
} else {
    echo "Click handler already exists\n";
}

echo "\nDone!\n";
