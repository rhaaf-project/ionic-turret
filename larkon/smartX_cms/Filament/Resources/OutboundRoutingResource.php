<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutboundRoutingResource\Pages;
use App\Models\OutboundRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OutboundRoutingResource extends Resource
{
    protected static ?string $model = OutboundRoute::class;

    protected static ?string $slug = 'outbound-routings';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Call Routing';

    protected static ?string $navigationLabel = 'Outbound';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Route Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select which PBX this route belongs to'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Out_Bquik_HarInd'),
                        Forms\Components\Select::make('trunk_id')
                            ->label('Trunk')
                            ->relationship('trunk', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select trunk to use for outbound calls'),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number = higher priority'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Dial Patterns')
                    ->description('Define which calls this route handles')
                    ->schema([
                        Forms\Components\TextInput::make('dial_pattern')
                            ->label('Dial Pattern')
                            ->default('.')
                            ->helperText('. = any number, X = single digit, [0-9] = range'),
                        Forms\Components\TextInput::make('match_cid')
                            ->label('Match Caller ID')
                            ->placeholder('e.g. 7432X')
                            ->helperText('Only extensions matching this pattern can use this route. X = any digit. Leave empty for all.'),
                        Forms\Components\TextInput::make('prepend_digits')
                            ->label('Prepend Digits')
                            ->placeholder('e.g. 0')
                            ->helperText('Digits to prepend before dialing'),
                    ])->columns(3),

                Forms\Components\Section::make('Caller ID Override')
                    ->schema([
                        Forms\Components\TextInput::make('outcid')
                            ->label('Override Caller ID')
                            ->placeholder('e.g. 02150877432')
                            ->helperText('Force this Caller ID for outgoing calls (optional)'),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trunk.name')
                    ->label('Trunk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('match_cid')
                    ->label('CID Pattern')
                    ->placeholder('Any'),
                Tables\Columns\TextColumn::make('dial_pattern')
                    ->label('Dial Pattern'),
                Tables\Columns\TextColumn::make('priority')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('trunk_id')
                    ->label('Trunk')
                    ->relationship('trunk', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active status'),
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
            'index' => Pages\ListOutboundRoutings::route('/'),
            'create' => Pages\CreateOutboundRouting::route('/create'),
            'edit' => Pages\EditOutboundRouting::route('/{record}/edit'),
        ];
    }
}
