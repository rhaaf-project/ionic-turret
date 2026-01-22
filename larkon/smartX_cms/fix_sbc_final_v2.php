<?php
// fix_sbc_final_v2.php

$path = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($path);

// --- Fix 1: Regular Branches ---
$search1 = <<<'PHP'
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

$replace1 = <<<'PHP'
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

// Normalize newlines just in case
$content = str_replace(str_replace("\r\n", "\n", $search1), str_replace("\r\n", "\n", $replace1), $content);
// Try exact match if normalization fails
$content = str_replace($search1, $replace1, $content);


// --- Fix 2: Orphan Branches ---
$search2 = <<<'PHP'
foreach ($orphanBranches as $branch) {
            $ip = $branch->callServer->host ?? 'N/A';
            $nodes[] = [
                'id' => 'br_' . $branch->id,
                'label' => $branch->name,
                'title' => "{$branch->name}\nType: standalone\nIP: {$ip}",
                'group' => 'standalone',
                'has_sbc' => \App\Models\Sbc::where('branch_id', $branch->id)->exists(),
                'ip' => $ip,
            ];
        }
PHP;

$replace2 = <<<'PHP'
foreach ($orphanBranches as $branch) {
            $ip = $branch->callServer->host ?? 'N/A';
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
        }
PHP;

$content = str_replace(str_replace("\r\n", "\n", $search2), str_replace("\r\n", "\n", $replace2), $content);
$content = str_replace($search2, $replace2, $content);

file_put_contents($path, $content);
echo "Applied fixes to InterconnectivityResource.php\n";
