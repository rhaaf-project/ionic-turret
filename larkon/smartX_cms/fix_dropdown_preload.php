<?php
/**
 * Minimal fix: Add ->preload() after ->searchable() for Call Server dropdown
 * Only fixes the specific resources that are broken
 */

$basePath = '/var/www/html/smartX/app/Filament/Resources';
$resources = [
    'LineItemResource.php',
    'ExtensionResource.php',
    'VpwResource.php',
    'CasResource.php',
    'ThirdPartyDeviceResource.php',
];

echo "=== Adding ->preload() to Call Server dropdowns ===\n\n";

foreach ($resources as $resource) {
    $file = $basePath . '/' . $resource;

    if (!file_exists($file)) {
        echo "⏭️  SKIP: $resource (file not found)\n";
        continue;
    }

    $content = file_get_contents($file);

    // Check if already has preload after searchable for call_server_id
    if (strpos($content, "->searchable()->preload()") !== false) {
        echo "✓  OK: $resource (already has preload)\n";
        continue;
    }

    // Find and replace only the call_server_id Select pattern
    // Pattern: ->searchable(), after relationship('callServer'
    $pattern = "/(->relationship\('callServer',\s*'name'\)\s*\n\s*->required\(\)\s*\n\s*->searchable\(\))(,)/";
    $replacement = "$1->preload()$2";

    $newContent = preg_replace($pattern, $replacement, $content);

    // If first pattern didn't match, try simpler pattern
    if ($newContent === $content) {
        // Simpler: just add preload after searchable for any Select with relationship
        $pattern2 = "/(->relationship\('callServer',\s*'name'\)[\s\S]*?->searchable\(\))(,)/";
        $replacement2 = "$1->preload()$2";
        $newContent = preg_replace($pattern2, $replacement2, $content, 1);
    }

    if ($newContent !== $content) {
        // Backup
        file_put_contents($file . '.dropdown_bak', $content);
        // Write fix
        file_put_contents($file, $newContent);
        echo "✅ FIXED: $resource\n";
    } else {
        echo "⚠️  NO CHANGE: $resource (pattern not found)\n";
    }
}

echo "\nDone! Run: php artisan optimize:clear\n";
