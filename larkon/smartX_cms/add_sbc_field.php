<?php
// Add has_sbc field to topology nodes in InterconnectivityResource

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($file);

// Add has_sbc to branch nodes - check if branch_id has SBC in sbc table
// For now, we'll add a simple check based on SBC model

// Find the node creation for branches and add has_sbc field
// Looking at line 61: $nodes[] = [ for branches

// Pattern for branch node (peer type)
$oldBranchNode = "'status' => \$branch->status ?? 'OK',";
$newBranchNode = "'status' => \$branch->status ?? 'OK',
                    'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),";

$content = str_replace($oldBranchNode, $newBranchNode, $content);

// Pattern for standalone branch node  
$oldStandaloneNode = "'group' => 'standalone',";
$newStandaloneNode = "'group' => 'standalone',
                'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),";

$content = str_replace($oldStandaloneNode, $newStandaloneNode, $content);

file_put_contents($file, $content);
echo "Added has_sbc field to InterconnectivityResource\n";
echo "Done!\n";
