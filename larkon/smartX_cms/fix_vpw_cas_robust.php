<?php
// fix_vpw_cas_robust.php
// Direct line-by-line fix for VPW and CAS forms

// VPW: Change 'vpw' field to 'vpw_number' and add hidden 'name' 
$vpwFile = '/var/www/html/smartX/app/Filament/Resources/VpwResource.php';
$lines = file($vpwFile);
$newLines = [];
$foundVpwField = false;

foreach ($lines as $i => $line) {
    // Replace vpw field with vpw_number
    if (strpos($line, "TextInput::make('vpw')") !== false) {
        $newLines[] = str_replace("make('vpw')", "make('vpw_number')", $line);
        $foundVpwField = true;
    }
    // After the vpw/vpw_number block, add hidden name field (after maxLength line)
    elseif ($foundVpwField && strpos($line, "->maxLength(255),") !== false) {
        $newLines[] = $line;
        // Add mutator to copy vpw_number to name
        $newLines[] = "                Forms\Components\Hidden::make('name'),\n";
        $foundVpwField = false; // Reset so we don't add again
    } else {
        $newLines[] = $line;
    }
}

file_put_contents($vpwFile, implode('', $newLines));
echo "Fixed VpwResource: vpw -> vpw_number + added Hidden name field\n";

// Now we need to add a mutator in the VPW model to auto-set name from vpw_number
$vpwModel = '/var/www/html/smartX/app/Models/Vpw.php';
$modelContent = file_get_contents($vpwModel);

// Add boot method to auto-set name from vpw_number if not already there
if (strpos($modelContent, 'static::creating') === false) {
    $bootMethod = "\n    protected static function boot()\n    {\n        parent::boot();\n        static::creating(function (\$model) {\n            if (empty(\$model->name) && !empty(\$model->vpw_number)) {\n                \$model->name = \$model->vpw_number;\n            }\n        });\n    }\n";

    // Insert before the last closing brace
    $modelContent = preg_replace('/}\s*$/', $bootMethod . "}\n", $modelContent);
    file_put_contents($vpwModel, $modelContent);
    echo "Added boot() to Vpw model to auto-set name from vpw_number\n";
}

// CAS: Change 'cas' to 'name' if it exists
$casFile = '/var/www/html/smartX/app/Filament/Resources/CasResource.php';
$casContent = file_get_contents($casFile);

if (strpos($casContent, "TextInput::make('cas')") !== false) {
    $casContent = str_replace("TextInput::make('cas')", "TextInput::make('name')", $casContent);
    file_put_contents($casFile, $casContent);
    echo "Fixed CasResource: cas -> name\n";
} else {
    echo "CasResource: 'cas' field not found (may already be fixed or named differently)\n";
    // Check current field name
    if (preg_match("/TextInput::make\\('([^']+)'\\)/", $casContent, $matches)) {
        echo "  Current CAS text input field: " . $matches[1] . "\n";
    }
}

// Verify fixes
echo "\n=== VERIFICATION ===\n";

$vpw = file_get_contents($vpwFile);
echo "VPW form has 'vpw_number': " . (strpos($vpw, "make('vpw_number')") !== false ? "YES" : "NO") . "\n";
echo "VPW form has hidden 'name': " . (strpos($vpw, "Hidden::make('name')") !== false ? "YES" : "NO") . "\n";

$cas = file_get_contents($casFile);
echo "CAS form has 'name' field: " . (strpos($cas, "TextInput::make('name')") !== false ? "YES" : "NO") . "\n";

echo "\nDone!\n";
