<?php
/**
 * Direct fix for LineItemResource - replace entire form method
 */

$file = '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php';
$content = file_get_contents($file);

// Backup
file_put_contents($file . '.v2_bak', $content);
echo "✅ Backup created\n";

// The old form method (what's currently in the file)
$oldForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable()->preload(),
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

// The corrected form method matching Line model
$newForm = <<<'PHP'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('line_number')
                    ->label('Line Number')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. 02150877466'),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Telkom Line 1'),
                Forms\Components\Select::make('type')
                    ->label('Line Type')
                    ->options(Line::getTypes())
                    ->required()
                    ->default('sip'),
                Forms\Components\TextInput::make('channel_count')
                    ->label('Channel Count')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(100),
                Forms\Components\Select::make('trunk_id')
                    ->label('Trunk')
                    ->relationship('trunk', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Optional'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
PHP;

$newContent = str_replace($oldForm, $newForm, $content);

if ($newContent === $content) {
    echo "⚠️ Pattern not found, trying alternative...\n";

    // Try with different whitespace
    $pattern = '/public static function form\(Form \$form\): Form\s*\{[^}]+\}\s*\}/s';
    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, $newForm, $content, 1);
    }
}

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "✅ LineItemResource form method replaced!\n";
    echo "\nNew form fields:\n";
    echo "  - call_server_id (Select)\n";
    echo "  - line_number (TextInput)\n";
    echo "  - name (TextInput)\n";
    echo "  - type (Select with Line types)\n";
    echo "  - channel_count (Number)\n";
    echo "  - trunk_id (Select, optional)\n";
    echo "  - description (Textarea)\n";
    echo "  - is_active (Toggle)\n";
} else {
    echo "❌ Could not apply fix - pattern mismatch\n";
    echo "Please check file manually.\n";
}

echo "\nRun: php artisan optimize:clear\n";
