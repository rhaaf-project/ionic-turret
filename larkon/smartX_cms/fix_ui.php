<?php
// Fix: remove widget from Private Wire, hide breadcrumbs, smaller buttons

// 1. Remove widget from ListPrivateWires
$file = '/var/www/html/smartX/app/Filament/Resources/PrivateWireResource/Pages/ListPrivateWires.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    // Remove getHeaderWidgets method if exists
    $content = preg_replace('/protected function getHeaderWidgets\(\): array\s*\{[^}]+\}/', '', $content);
    file_put_contents($file, $content);
    echo "Removed widgets from ListPrivateWires\n";
}

// 2. Hide breadcrumbs in AdminPanelProvider
$provider = '/var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($provider);
if (strpos($content, 'breadcrumbs(false)') === false) {
    $content = str_replace(
        '->discoverResources',
        "->breadcrumbs(false)\n            ->discoverResources",
        $content
    );
    file_put_contents($provider, $content);
    echo "Added breadcrumbs(false) to AdminPanelProvider\n";
}

// 3. Create custom CSS to make buttons smaller
$cssPath = '/var/www/html/smartX/resources/css/custom.css';
$css = "
/* Smaller create buttons */
.fi-btn {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
    padding-top: 0.375rem !important;
    padding-bottom: 0.375rem !important;
    font-size: 0.75rem !important;
}

.fi-btn-icon {
    width: 1rem !important;
    height: 1rem !important;
}
";
file_put_contents($cssPath, $css);
echo "Created custom.css for smaller buttons\n";

echo "Done!\n";
