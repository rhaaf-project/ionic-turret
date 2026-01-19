<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeadOfficeResource\Pages;
use App\Models\CallServer;
use App\Models\HeadOffice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeadOfficeResource extends Resource
{
    protected static ?string $model = HeadOffice::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Head Office';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Head Office Details')
                    ->headerActions([
                        Forms\Components\Actions\Action::make('active_status')
                            ->label(fn(Get $get) => $get('is_active') ? 'Active' : 'Inactive')
                            ->badge()
                            ->color(fn(Get $get) => $get('is_active') ? 'success' : 'danger')
                            ->disabled(),
                    ])
                    ->schema([
                        // Row 1: Company, Name, Code, Active
                        Forms\Components\Select::make('customer_id')
                            ->label('Company')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('code')->maxLength(20),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. HO Jakarta'),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(20)
                            ->placeholder('e.g. HO-JKT'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->live(),
                        // Row 2: Site Type, BCP/DRC Server, Enabled
                        Forms\Components\Select::make('type')
                            ->label('Site Type')
                            ->options(HeadOffice::getTypes())
                            ->default('ha')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $count = match ($state) {
                                    'ha' => 3,
                                    'fo' => 2,
                                    'basic' => 1,
                                    default => 0,
                                };
                                $items = array_fill(0, $count, ['call_server_id' => null, 'is_enabled' => true]);
                                $set('call_servers', $items);
                            }),
                        Forms\Components\Select::make('bcp_drc_server_id')
                            ->label('BCP/DRC Server')
                            ->placeholder('Optional backup server')
                            ->options(fn() => CallServer::all()->mapWithKeys(fn($cs) => [
                                $cs->id => $cs->name . ' - ' . $cs->host
                            ]))
                            ->searchable()
                            ->helperText('Backup server for disaster recovery'),
                        Forms\Components\Toggle::make('bcp_drc_enabled')
                            ->label('Enabled')
                            ->default(false)
                            ->inline(false),
                        Forms\Components\Placeholder::make('spacer')->label('')->hiddenLabel(),
                    ])->columns(4),

                // Dynamic Call Servers Section
                Forms\Components\Section::make('Call Servers')
                    ->description(fn(Get $get) => match ($get('type')) {
                        'ha' => 'High Availability requires 3 Call Servers',
                        'fo' => 'Failover/Redundancy requires 2 Call Servers',
                        'basic' => 'Basic site uses 1 Call Server',
                        default => 'Select Call Servers for this site',
                    })
                    ->schema([
                        Forms\Components\Repeater::make('call_servers')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('call_server_id')
                                    ->label('Call Server')
                                    ->options(fn() => CallServer::all()->mapWithKeys(fn($cs) => [
                                        $cs->id => $cs->name . ' - ' . $cs->host
                                    ]))
                                    ->required()
                                    ->searchable()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(2),
                                Forms\Components\Toggle::make('is_enabled')
                                    ->label('Enabled')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(3)
                            ->minItems(fn(Get $get) => match ($get('type')) {
                                'ha' => 3,
                                'fo' => 2,
                                'basic' => 1,
                                default => 0,
                            })
                            ->maxItems(fn(Get $get) => match ($get('type')) {
                                'ha' => 3,
                                'fo' => 2,
                                'basic' => 1,
                                default => 10,
                            })
                            ->defaultItems(fn(Get $get) => match ($get('type')) {
                                'ha' => 3,
                                'fo' => 2,
                                'basic' => 1,
                                default => 0,
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->itemLabel(fn(array $state): string => $state['call_server_id'] ? 'Server' : 'Select Server')
                            ->collapsible(false),
                    ])
                    ->visible(fn(Get $get) => in_array($get('type'), ['ha', 'fo', 'basic'])),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('country')
                            ->default('Indonesia')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('province')
                            ->maxLength(50)
                            ->placeholder('e.g. DKI Jakarta'),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(50)
                            ->placeholder('e.g. Jakarta Pusat'),
                        Forms\Components\TextInput::make('district')
                            ->maxLength(50)
                            ->placeholder('e.g. Menteng'),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(4),

                Forms\Components\Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => HeadOffice::getTypes()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'basic' => 'gray',
                        'ha' => 'success',
                        'fo' => 'warning',
                        'bcp' => 'info',
                        'drc' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('city')
                    ->label('City'),
                Tables\Columns\TextColumn::make('call_servers_count')
                    ->label('Servers')
                    ->counts('callServers'),
                Tables\Columns\TextColumn::make('branches_count')
                    ->label('Branches')
                    ->counts('branches'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Company')
                    ->relationship('customer', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(HeadOffice::getTypes()),
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
            'index' => Pages\ListHeadOffices::route('/'),
            'create' => Pages\CreateHeadOffice::route('/create'),
            'edit' => Pages\EditHeadOffice::route('/{record}/edit'),
        ];
    }
}
