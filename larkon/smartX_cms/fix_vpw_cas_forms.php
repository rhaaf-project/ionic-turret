<?php
// fix_vpw_cas_forms.php
// Fix VPW and CAS forms to match user requirements while satisfying database constraints

// VPW Form: Call Server, VPW (vpw_number), Destination Local, Destination
// CAS Form: Call Server, CAS (should map to a field), Destination Local, Destination

// Since database has different columns, we need to:
// 1. Make 'name' column nullable or use vpw/cas value as name
// 2. Map form field 'vpw' to 'vpw_number' and set 'name' to same value
// 3. Map form field 'cas' to name, and add destination columns

// First, update VpwResource form
$vpwFile = '/var/www/html/smartX/app/Filament/Resources/VpwResource.php';
$vpwContent = file_get_contents($vpwFile);

// Replace vpw field with vpw_number, and add mutators
$oldVpwForm = "Forms\Components\TextInput::make('vpw')
                    ->label('VPW')
                    ->required()
                    ->maxLength(255),";

$newVpwForm = "Forms\Components\TextInput::make('vpw_number')
                    ->label('VPW')
                    ->required()
                    ->afterStateUpdated(fn (\$state, callable \$set) => \$set('name', \$state))
                    ->live()
                    ->maxLength(255),
                Forms\Components\Hidden::make('name'),";

$vpwContent = str_replace($oldVpwForm, $newVpwForm, $vpwContent);
file_put_contents($vpwFile, $vpwContent);
echo "Fixed VpwResource form (vpw -> vpw_number + auto-set name)\n";

// Update CAS form - CAS has different structure, need destination_local
$casFile = '/var/www/html/smartX/app/Filament/Resources/CasResource.php';
$casContent = file_get_contents($casFile);

$oldCasForm = "Forms\Components\TextInput::make('cas')
                    ->label('CAS')
                    ->required()
                    ->maxLength(255),";

$newCasForm = "Forms\Components\TextInput::make('name')
                    ->label('CAS')
                    ->required()
                    ->maxLength(255),";

$casContent = str_replace($oldCasForm, $newCasForm, $casContent);
file_put_contents($casFile, $casContent);
echo "Fixed CasResource form (cas -> name)\n";

// Also need to add destination_local to vpws and cas tables
try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Check and add destination_local to vpws
    if (!Schema::hasColumn('vpws', 'destination_local')) {
        DB::statement('ALTER TABLE vpws ADD COLUMN destination_local BOOLEAN DEFAULT FALSE');
        echo "Added destination_local to vpws table\n";
    }

    // Check and add destination_local to cas
    if (!Schema::hasColumn('cas', 'destination_local')) {
        DB::statement('ALTER TABLE cas ADD COLUMN destination_local BOOLEAN DEFAULT FALSE');
        echo "Added destination_local to cas table\n";
    }
    if (!Schema::hasColumn('cas', 'destination')) {
        DB::statement('ALTER TABLE cas ADD COLUMN destination VARCHAR(255) NULL');
        echo "Added destination to cas table\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

// Update Vpw model fillable
$vpwModel = '/var/www/html/smartX/app/Models/Vpw.php';
$vpwModelContent = file_get_contents($vpwModel);
if (strpos($vpwModelContent, "'destination_local'") === false) {
    $vpwModelContent = str_replace(
        "'destination',",
        "'destination',\n        'destination_local',",
        $vpwModelContent
    );
    file_put_contents($vpwModel, $vpwModelContent);
    echo "Added destination_local to Vpw model fillable\n";
}

// Update Cas model fillable
$casModel = '/var/www/html/smartX/app/Models/Cas.php';
$casModelContent = file_get_contents($casModel);
if (strpos($casModelContent, "'destination'") === false) {
    $casModelContent = str_replace(
        "'description',",
        "'description',\n        'destination',\n        'destination_local',",
        $casModelContent
    );
    file_put_contents($casModel, $casModelContent);
    echo "Added destination, destination_local to Cas model fillable\n";
}

echo "\nDone!\n";
