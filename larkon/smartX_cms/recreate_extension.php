<?php
// Recreate clean ExtensionResource

$file = '/var/www/html/smartX/app/Filament/Resources/ExtensionResource.php';

$content = <<<'PHP'
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtensionResource\Pages;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    
    protected static ?string $navigationLabel = 'Extensions';
    
    protected static ?string $navigationParentItem = 'Line';
    
    protected static ?int $navigationSort = 2;

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtensions::route('/'),
            'create' => Pages\CreateExtension::route('/create'),
            'edit' => Pages\EditExtension::route('/{record}/edit'),
        ];
    }
}
PHP;

file_put_contents($file, $content);
echo "Recreated ExtensionResource.php\n";
echo "Done!\n";
