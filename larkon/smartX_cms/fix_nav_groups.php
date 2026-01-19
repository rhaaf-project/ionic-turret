<?php
// Fix navigation groups order by adding navigationGroups to AdminPanelProvider

$file = '/var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($file);

$navigationGroups = <<<'PHP'
->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Organization')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Connectivity')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Recording')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Log')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Turret Management')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Device')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('User')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Network')->collapsible(false),
                \Filament\Navigation\NavigationGroup::make('Administration')->collapsible(false),
            ])
            ->discoverResources
PHP;

// Insert before ->discoverResources
$content = str_replace(
    '->discoverResources',
    $navigationGroups,
    $content
);

file_put_contents($file, $content);
echo "Added navigationGroups to AdminPanelProvider!\n";
