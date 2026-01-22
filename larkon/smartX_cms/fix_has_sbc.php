<?php
// Fix: add has_sbc to branch group nodes as well

$file = '/var/www/html/smartX/app/Filament/Resources/InterconnectivityResource.php';
$content = file_get_contents($file);

// Add has_sbc to branch group nodes
$oldNode = "'group' => 'branch',
                    'status' => \$status,
                    'ip' => \$ip,
                ];";

$newNode = "'group' => 'branch',
                    'status' => \$status,
                    'ip' => \$ip,
                    'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),
                ];";

if (
    strpos($content, "'has_sbc' => \\App\\Models\\Sbc::where('branch_id', \$branch->id)->exists(),
                ];") === false
) {
    $content = str_replace($oldNode, $newNode, $content);
    file_put_contents($file, $content);
    echo "Added has_sbc to branch group nodes\n";
} else {
    echo "has_sbc already exists in branch group\n";
}

echo "Done!\n";
