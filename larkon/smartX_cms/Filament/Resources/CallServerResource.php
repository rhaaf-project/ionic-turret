<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallServerResource\Pages;
use App\Models\CallServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CallServerResource extends Resource
{
    protected static ?string $model = CallServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationLabel = 'Call Server';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Call Server Details')
                    ->schema([
                        Forms\Components\Select::make('head_office_id')
                            ->label('Head Office Site')
                            ->relationship('headOffice', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select HO site (optional)')
                            ->helperText('Which HO site is this server deployed at?'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. SmartUCX-1'),
                        Forms\Components\TextInput::make('host')
                            ->label('Host / IP Address')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. 103.154.80.172'),
                        Forms\Components\TextInput::make('port')
                            ->numeric()
                            ->default(5060)
                            ->minValue(1)
                            ->maxValue(65535),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('headOffice.name')
                    ->label('HO Site')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('host')
                    ->label('Host / IP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('extensions_count')
                    ->label('Ext')
                    ->counts('extensions'),
                Tables\Columns\TextColumn::make('lines_count')
                    ->label('Lines')
                    ->counts('lines'),
                Tables\Columns\TextColumn::make('trunks_count')
                    ->label('Trunks')
                    ->counts('trunks'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCallServers::route('/'),
            'create' => Pages\CreateCallServer::route('/create'),
            'view' => Pages\ViewCallServer::route('/{record}'),
            'edit' => Pages\EditCallServer::route('/{record}/edit'),
        ];
    }
}
