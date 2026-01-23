<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SbcResource\Pages;
use App\Models\Sbc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SbcResource extends Resource
{
    protected static ?string $model = Sbc::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'SBC ▾';

    protected static ?string $navigationLabel = 'Connections';

    protected static ?string $modelLabel = 'SBC Connection';

    protected static ?string $pluralModelLabel = 'SBC Connections';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SBC Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. SBC_Main'),
                        Forms\Components\TextInput::make('outcid')
                            ->label('Outbound Caller ID')
                            ->maxLength(255)
                            ->placeholder('e.g. 02150877432'),
                        Forms\Components\TextInput::make('maxchans')
                            ->label('Max Channels')
                            ->numeric()
                            ->default(2)
                            ->minValue(1)
                            ->maxValue(100),
                        Forms\Components\Toggle::make('disabled')
                            ->label('Disabled')
                            ->default(false)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('SIP Server Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sip_server')
                            ->label('SIP Server')
                            ->required()
                            ->placeholder('e.g. 13.44.13.4 or sbc.provider.com'),
                        Forms\Components\TextInput::make('sip_server_port')
                            ->label('SIP Port')
                            ->numeric()
                            ->default(5060)
                            ->minValue(1)
                            ->maxValue(65535),
                        Forms\Components\Select::make('transport')
                            ->options(Sbc::getTransports())
                            ->default('udp')
                            ->required(),
                        Forms\Components\TextInput::make('context')
                            ->default('from-pstn')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Codec & DTMF')
                    ->schema([
                        Forms\Components\TextInput::make('codecs')
                            ->default('ulaw,alaw')
                            ->helperText('Comma-separated codec list'),
                        Forms\Components\Select::make('dtmfmode')
                            ->label('DTMF Mode')
                            ->options(Sbc::getDtmfModes())
                            ->default('auto'),
                    ])->columns(2),

                Forms\Components\Section::make('Registration & Authentication')
                    ->schema([
                        Forms\Components\Select::make('registration')
                            ->options(Sbc::getRegistrations())
                            ->default('none'),
                        Forms\Components\TextInput::make('auth_username')
                            ->label('Auth Username')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('secret')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->maxLength(100),
                    ])->columns(3),

                Forms\Components\Section::make('Qualify Settings')
                    ->schema([
                        Forms\Components\Toggle::make('qualify')
                            ->label('Enable Qualify')
                            ->default(true)
                            ->inline(false),
                        Forms\Components\TextInput::make('qualify_frequency')
                            ->label('Qualify Frequency (seconds)')
                            ->numeric()
                            ->default(60)
                            ->minValue(10)
                            ->maxValue(300),
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
                Tables\Columns\TextColumn::make('sip_server')
                    ->label('SIP Server'),
                Tables\Columns\TextColumn::make('outcid')
                    ->label('Outbound CID')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('maxchans')
                    ->label('Max Ch.')
                    ->numeric(),
                Tables\Columns\IconColumn::make('disabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Status')
                    ->getStateUsing(fn($record) => !$record->disabled),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name'),
                Tables\Filters\TernaryFilter::make('disabled')
                    ->label('Status')
                    ->trueLabel('Disabled')
                    ->falseLabel('Enabled')
                    ->placeholder('All'),
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
            'index' => Pages\ListSbcs::route('/'),
            'create' => Pages\CreateSbc::route('/create'),
            'edit' => Pages\EditSbc::route('/{record}/edit'),
        ];
    }
}
