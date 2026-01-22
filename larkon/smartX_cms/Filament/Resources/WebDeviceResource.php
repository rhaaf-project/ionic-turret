<?php
// 3rd Party (SIP Softphone) Resource - Connectivity Group
namespace App\Filament\Resources;

use App\Filament\Resources\WebDeviceResource\Pages;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebDeviceResource extends Resource
{
    protected static ?string $model = Extension::class;
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Line ▾';
    protected static ?string $navigationLabel = '3rd Party';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('3rd Party SIP Device')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('extension')
                            ->label('Extension')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('secret')
                            ->label('Secret')
                            ->default(fn() => bin2hex(random_bytes(8)))
                            ->required()
                            ->maxLength(32)
                            ->helperText('Auto-generated. You can customize if needed.'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('type')
                            ->default('softphone'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callServer.name')
                    ->label('Call Server')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extension')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secret')
                    ->label('Secret')
                    ->copyable(),
            ])
            ->modifyQueryUsing(fn($query) => $query->where('type', 'softphone'))
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebDevices::route('/'),
            'create' => Pages\CreateWebDevice::route('/create'),
            'edit' => Pages\EditWebDevice::route('/{record}/edit'),
        ];
    }
}
