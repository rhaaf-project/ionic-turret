<?php
// Fix navigation group order by updating all resources
// Order: Organization(1), Connectivity(2), Recording(3), Log(4), Turret Management(5), Device(6), User(7), Network(8), Administrator(9)

$basePath = '/var/www/html/smartX/app/Filament/Resources';

$groupOrder = [
    'Organization' => 1,
    'Connectivity' => 2,
    'Recording' => 3,
    'Log' => 4,
    'Turret Management' => 5,
    'Device' => 6,
    'User' => 7,
    'Network' => 8,
    'Administration' => 9,
    'Administrator' => 9,
];

$files = glob("$basePath/*.php");
foreach ($files as $file) {
    $content = file_get_contents($file);

    // Find current group
    if (preg_match("/navigationGroup\s*=\s*'([^']+)'/", $content, $matches)) {
        $group = $matches[1];
        $order = $groupOrder[$group] ?? 99;

        // Add or update navigationGroupSort
        if (preg_match("/protected static \?int \\\$navigationGroupSort/", $content)) {
            // Update existing
            $content = preg_replace(
                "/protected static \?int \\\$navigationGroupSort\s*=\s*\d+;/",
                "protected static ?int \$navigationGroupSort = $order;",
                $content
            );
        } else {
            // Add after navigationGroup line
            $content = preg_replace(
                "/(protected static \?string \\\$navigationGroup\s*=\s*'[^']+';)/",
                "$1\n    protected static ?int \$navigationGroupSort = $order;",
                $content
            );
        }

        file_put_contents($file, $content);
        echo "Updated: " . basename($file) . " (Group: $group, Order: $order)\n";
    }
}

echo "\nDone! Run: php artisan optimize:clear\n";
