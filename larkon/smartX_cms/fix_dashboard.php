<?php
// Fix dashboard widgets and remove welcome/filament sections

// 1. Enable widget auto-discovery
$widgets = [
    '/var/www/html/smartX/app/Filament/Widgets/StatsOverview.php',
    '/var/www/html/smartX/app/Filament/Widgets/PrivateWireStats.php',
];

foreach ($widgets as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(
            'protected static bool $isDiscovered = false;',
            'protected static bool $isDiscovered = true;',
            $content
        );
        file_put_contents($file, $content);
        echo "Fixed widget: $file\n";
    }
}

// 2. Remove AccountWidget and FilamentInfoWidget from AdminPanelProvider
$provider = '/var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($provider);
$content = str_replace(
    'Widgets\AccountWidget::class,',
    '// Widgets\AccountWidget::class,',
    $content
);
$content = str_replace(
    'Widgets\FilamentInfoWidget::class,',
    '// Widgets\FilamentInfoWidget::class,',
    $content
);
file_put_contents($provider, $content);
echo "Fixed AdminPanelProvider\n";

echo "Done!\n";
