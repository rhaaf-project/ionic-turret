<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtensionResource\Pages;
use App\Models\Extension;
use App\Services\AsteriskService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Line ▾';

    protected static ?int $navigationSort = 2;

    /**
     * Get registration status from Asterisk (cached for 10 seconds)
     */
    protected static function getRegistrationStatus(): array
    {
        return Cache::remember('asterisk_endpoints', 10, function () {
            try {
                $asterisk = app(AsteriskService::class);
                $output = $asterisk->getEndpoints();

                $status = [];
                preg_match_all('/Endpoint:\s+(\d+)\s+(\w+(?:\s+\w+)?)/m', $output, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $ext = $match[1];
                    $state = trim($match[2]);
                    $status[$ext] = $state;
                }

                return $status;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Extension Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('extension')
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
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secret')
                    ->label('Secret')
                    ->copyable(),
            ])
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

    public static function getRelations(): array
    {
        return [];
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
