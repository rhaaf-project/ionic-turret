<?php
// Check if sbc_id is in topology data

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = App\Filament\Resources\InterconnectivityResource::getTopologyData();

echo "=== NODES WITH SBC ===\n";
foreach ($data['nodes'] as $node) {
    if (isset($node['has_sbc']) && $node['has_sbc']) {
        echo "Node: " . $node['label'] . "\n";
        echo "  has_sbc: " . ($node['has_sbc'] ? 'true' : 'false') . "\n";
        echo "  sbc_id: " . ($node['sbc_id'] ?? 'NOT SET') . "\n";
        echo "\n";
    }
}

echo "=== ALL NODES (first 3) ===\n";
for ($i = 0; $i < min(3, count($data['nodes'])); $i++) {
    print_r($data['nodes'][$i]);
}
