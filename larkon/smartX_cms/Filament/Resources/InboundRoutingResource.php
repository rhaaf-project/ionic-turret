<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InboundRoutingResource\Pages;
use App\Models\InboundRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InboundRoutingResource extends Resource
{
    protected static ?string $model = InboundRoute::class;

    protected static ?string $slug = 'inbound-routings';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Call Routing';

    protected static ?string $navigationLabel = 'Inbound';

    protected static ?int $navigationSort = 1;

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
                        Forms\Components\TextInput::make('did_number')
                            ->label('DID / Number')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g. 02150877432')
                            ->helperText('Incoming number to match'),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(100)
                            ->placeholder('e.g. HarInd Main Line'),
                        Forms\Components\Select::make('trunk_id')
                            ->label('From Trunk')
                            ->relationship('trunk', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Any trunk')
                            ->helperText('Optional: only match calls from this trunk'),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower = higher priority'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Destination')
                    ->schema([
                        Forms\Components\Select::make('destination_type')
                            ->label('Route To')
                            ->options(InboundRoute::getDestinationTypes())
                            ->default('extension')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('destination_id')
                            ->label('Extension')
                            ->relationship('destinationExtension', 'extension')
                            ->searchable()
                            ->preload()
                            ->visible(fn($get) => $get('destination_type') === 'extension')
                            ->helperText('Select extension to ring'),
                        Forms\Components\TextInput::make('destination_id')
                            ->label('Destination ID')
                            ->numeric()
                            ->visible(fn($get) => in_array($get('destination_type'), ['ring_group', 'ivr', 'voicemail']))
                            ->helperText('ID of the destination'),
                    ])->columns(2),

                Forms\Components\Section::make('Caller ID Filter')
                    ->schema([
                        Forms\Components\TextInput::make('cid_filter')
                            ->label('Caller ID Filter')
                            ->placeholder('e.g. 021* or leave empty for any')
                            ->helperText('Only match calls FROM this caller ID pattern'),
                    ]),
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
                Tables\Columns\TextColumn::make('did_number')
                    ->label('DID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('trunk.name')
                    ->label('Trunk')
                    ->placeholder('Any'),
                Tables\Columns\TextColumn::make('destination_type')
                    ->label('Dest Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => InboundRoute::getDestinationTypes()[$state] ?? $state),
                Tables\Columns\TextColumn::make('destination_id')
                    ->label('Dest ID'),
                Tables\Columns\TextColumn::make('priority')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('destination_type')
                    ->options(InboundRoute::getDestinationTypes()),
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
            'index' => Pages\ListInboundRoutings::route('/'),
            'create' => Pages\CreateInboundRouting::route('/create'),
            'edit' => Pages\EditInboundRouting::route('/{record}/edit'),
        ];
    }
}
