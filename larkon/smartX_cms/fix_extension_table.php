<?php
// Fix ExtensionResource - rebuild table method properly

$file = '/var/www/html/smartX/app/Filament/Resources/ExtensionResource.php';
$content = file_get_contents($file);

// Find and replace the entire table method with a clean version without Type column
$newTable = <<<'PHP'
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callServer.name')
                    ->label('Call Server')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('extension')
                    ->label('Extension')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sip_status')
                    ->label('SIP Status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
PHP;

// Replace the entire table method
$pattern = '/public static function table\(Table \$table\): Table\s*\{.*?return \$table[^}]+(?:\{[^}]*\}[^}]*)*\s*\}/s';

if (preg_match($pattern, $content)) {
    $content = preg_replace($pattern, $newTable, $content);
    file_put_contents($file, $content);
    echo "Fixed ExtensionResource table method\n";
} else {
    // Alternative: replace from 'public static function table' to next 'public static function'
    $start = strpos($content, 'public static function table(Table $table)');
    if ($start !== false) {
        $nextMethod = strpos($content, 'public static function', $start + 10);
        if ($nextMethod !== false) {
            $content = substr($content, 0, $start) . $newTable . "\n\n    " . substr($content, $nextMethod);
            file_put_contents($file, $content);
            echo "Fixed ExtensionResource table method (alt method)\n";
        }
    }
}

echo "Done!\n";
