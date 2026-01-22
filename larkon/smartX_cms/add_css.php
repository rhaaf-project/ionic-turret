<?php
// Add custom CSS to panel and fix button size

$provider = '/var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($provider);

// Check if renderHook is already present
if (strpos($content, 'renderHook') === false) {
    $css = "
<style>
.fi-btn { 
    padding: 0.375rem 0.75rem !important; 
    font-size: 0.75rem !important; 
}
.fi-btn-icon { 
    width: 1rem !important; 
    height: 1rem !important; 
}
</style>
";

    $renderHook = "->renderHook(
                'panels::head.end',
                fn () => '$css'
            )
            ->discoverResources";

    $content = str_replace('->discoverResources', $renderHook, $content);
    file_put_contents($provider, $content);
    echo "Added renderHook for custom CSS\n";
} else {
    echo "renderHook already exists\n";
}

echo "Done!\n";
