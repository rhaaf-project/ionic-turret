<?php
// Simplify Line, Extension, VPW, CAS, 3rd Party resource forms

$basePath = '/var/www/html/smartX/app/Filament/Resources';

// 1. Line Resource Form - 5 fields: call_server, line, name, secret, description
$lineForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('line')
                    ->label('Line')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->label('Secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Hidden::make('type')
                    ->default('webrtc'),
            ]);
    }
PHP;

// 2. Extension Resource Form - 5 fields: call_server, extension, name, secret, description
$extensionForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('extension')
                    ->label('Extension')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->label('Secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Hidden::make('type')
                    ->default('webrtc'),
            ]);
    }
PHP;

// 3. VPW Resource Form - 4 fields: call_server, vpw, destination_local (checkbox), destination
$vpwForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('vpw')
                    ->label('VPW')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('destination_local')
                    ->label('Destination Local')
                    ->default(false),
                Forms\Components\TextInput::make('destination')
                    ->label('Destination')
                    ->maxLength(255)
                    ->placeholder('Nomor tujuan'),
                Forms\Components\Hidden::make('type')
                    ->default('webrtc'),
            ]);
    }
PHP;

// 4. CAS Resource Form - 4 fields: call_server, cas, destination_local (checkbox), destination
$casForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('cas')
                    ->label('CAS')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('destination_local')
                    ->label('Destination Local')
                    ->default(false),
                Forms\Components\TextInput::make('destination')
                    ->label('Destination')
                    ->maxLength(255)
                    ->placeholder('Nomor tujuan'),
                Forms\Components\Hidden::make('type')
                    ->default('webrtc'),
            ]);
    }
PHP;

// 5. ThirdPartyDevice Resource Form - 5 fields: call_server, extension, name, secret, description
$thirdPartyForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('extension')
                    ->label('Extension')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->label('Secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Hidden::make('type')
                    ->default('softphone'),
            ]);
    }
PHP;

// Function to replace form method in a resource file
function replaceFormMethod($filePath, $newForm)
{
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }

    $content = file_get_contents($filePath);

    // Pattern to match the entire form method
    $pattern = '/public static function form\(Form \$form\): Form\s*\{[^}]+(?:\{[^}]*\}[^}]*)*\}/s';

    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $newForm, $content);
        file_put_contents($filePath, $content);
        echo "Updated: " . basename($filePath) . "\n";
        return true;
    } else {
        echo "Form method not found in: $filePath\n";
        return false;
    }
}

// Update resources
replaceFormMethod("$basePath/LineResource.php", $lineForm);
replaceFormMethod("$basePath/ExtensionResource.php", $extensionForm);
replaceFormMethod("$basePath/VpwResource.php", $vpwForm);
replaceFormMethod("$basePath/CasResource.php", $casForm);
replaceFormMethod("$basePath/ThirdPartyDeviceResource.php", $thirdPartyForm);

echo "\nDone! Run: php artisan optimize:clear\n";
