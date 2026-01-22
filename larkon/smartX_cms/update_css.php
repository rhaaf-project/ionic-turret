<?php
// Update CSS styles

$provider = '/var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($provider);

// Replace old CSS with new CSS
$oldCss = "
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

$newCss = "
<style>
.fi-btn { 
    padding: 2px 5px !important; 
    font-size: 0.75rem !important; 
}
.fi-btn-icon { 
    width: 1rem !important; 
    height: 1rem !important; 
}
section.flex.flex-col.gap-y-8 {
    gap: 1rem !important;
}
.gap-y-8 {
    gap: 1rem !important;
}
.py-8 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}
</style>
";

$content = str_replace($oldCss, $newCss, $content);
file_put_contents($provider, $content);
echo "Updated CSS styles\n";

echo "Done!\n";
