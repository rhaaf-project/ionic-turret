<?php
// Script to update navigation structure across all resources
// Run: php update_navigation.php

$basePath = '/var/www/html/smartX/app/Filament/Resources';

$updates = [
    // Connectivity Menu - order and structure
    'CallServerResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Call Server',
        'navigationSort' => 1,
        'navigationParentItem' => null,
    ],
    'LineResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Line ▾',
        'navigationSort' => 2,
        'navigationParentItem' => null,
    ],
    'LineItemResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Line ▾',
        'navigationLabel' => 'Line',
        'navigationSort' => 1,
    ],
    'ExtensionResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Line ▾',
        'navigationLabel' => 'Extension',
        'navigationSort' => 2,
    ],
    'VpwResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Line ▾',
        'navigationLabel' => 'VPW',
        'navigationSort' => 3,
    ],
    'CasResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Line ▾',
        'navigationLabel' => 'CAS',
        'navigationSort' => 4,
    ],
    'ThirdPartyDeviceResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Line ▾',
        'navigationLabel' => '3rd Party',
        'navigationSort' => 5,
    ],
    'TrunkResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Trunk',
        'navigationSort' => 3,
        'navigationParentItem' => null,
    ],
    'SbcResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'SBC',
        'navigationSort' => 4,
        'navigationParentItem' => null,
    ],
    'CallRoutingResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Call Routing ▾',
        'navigationSort' => 5,
        'navigationParentItem' => null,
    ],
    'InboundRoutingResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Call Routing ▾',
        'navigationLabel' => 'Inbound',
        'navigationSort' => 1,
    ],
    'OutboundRoutingResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationParentItem' => 'Call Routing ▾',
        'navigationLabel' => 'Outbound',
        'navigationSort' => 2,
    ],
    'PrivateWireResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Private Wire',
        'navigationSort' => 6,
        'navigationParentItem' => null,
    ],
    'IntercomResource.php' => [
        'navigationGroup' => 'Connectivity',
        'navigationLabel' => 'Intercom',
        'navigationSort' => 7,
        'navigationParentItem' => null,
    ],

    // Recording Menu
    'RecordingServerResource.php' => [
        'navigationGroup' => 'Recording',
        'navigationLabel' => 'Rec Server',
        'navigationSort' => 1,
        'navigationParentItem' => null,
    ],
    'DevTurretRecordingResource.php' => [
        'navigationGroup' => 'Recording',
        'navigationLabel' => 'Dev Turret',
        'navigationSort' => 2,
        'navigationParentItem' => null,
    ],

    // Administration Menu
    'UserResource.php' => [
        'navigationGroup' => 'Administration',
        'navigationLabel' => 'CMS Admins',
        'navigationSort' => 1,
        'navigationParentItem' => null,
    ],
    'PolicyRoleResource.php' => [
        'navigationGroup' => 'Administration',
        'navigationLabel' => 'Role/Privilege',
        'navigationSort' => 2,
        'navigationParentItem' => null,
    ],
];

foreach ($updates as $file => $settings) {
    $filePath = "$basePath/$file";
    if (!file_exists($filePath)) {
        echo "SKIP: $file (not found)\n";
        continue;
    }

    $content = file_get_contents($filePath);
    $modified = false;

    foreach ($settings as $prop => $value) {
        $pattern = "/protected static \?string \\\$$prop = '[^']*';/";

        if ($value === null) {
            // Remove the line if exists
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern . "\r?\n?/", '', $content);
                $modified = true;
            }
        } else {
            if ($prop === 'navigationSort') {
                $pattern = "/protected static \?int \\\$$prop = \d+;/";
                $replacement = "protected static ?int \$$prop = $value;";
            } else {
                $replacement = "protected static ?string \$$prop = '$value';";
            }

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
                $modified = true;
            }
        }
    }

    if ($modified) {
        file_put_contents($filePath, $content);
        echo "UPDATED: $file\n";
    } else {
        echo "NO CHANGE: $file\n";
    }
}

echo "\nDone! Run: php artisan optimize:clear\n";
