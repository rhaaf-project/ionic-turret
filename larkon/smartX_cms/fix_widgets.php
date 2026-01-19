<?php
// Quick fix - disable widget auto-discovery on Dashboard
$widgets = [
    '/var/www/html/smartX/app/Filament/Widgets/StatsOverview.php',
    '/var/www/html/smartX/app/Filament/Widgets/PrivateWireStats.php',
];

foreach ($widgets as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Add isDiscovered = false after the class opening
        if (strpos($content, 'isDiscovered') === false) {
            $content = preg_replace(
                '/class (\w+) extends Widget\s*\{/',
                "class $1 extends Widget\n{\n    protected static bool \$isDiscovered = false;",
                $content
            );
            file_put_contents($file, $content);
            echo "Fixed: $file\n";
        } else {
            echo "Already has isDiscovered: $file\n";
        }
    }
}
echo "Done!\n";
