<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VpwResource\Pages;
use App\Models\Vpw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VpwResource extends Resource
{
    protected static ?string $model = Vpw::class;

    protected static ?string $slug = 'vpws';

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Line ▾';

    protected static ?string $navigationLabel = 'VPW';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('VPW Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('vpw_number')
                            ->label('VPW')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Toggle::make('destination_local')
                            ->label('Destination Local')
                            ->default(false)
                            ->inline(false),
                        Forms\Components\TextInput::make('destination')
                            ->label('Destination')
                            ->maxLength(100)
                            ->helperText('Nomor tujuan'),
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
                Tables\Columns\TextColumn::make('vpw_number')
                    ->label('VPW')
                    ->searchable(),
                Tables\Columns\IconColumn::make('destination_local')
                    ->boolean()
                    ->label('Local'),
                Tables\Columns\TextColumn::make('destination')
                    ->limit(30),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVpws::route('/'),
            'create' => Pages\CreateVpw::route('/create'),
            'edit' => Pages\EditVpw::route('/{record}/edit'),
        ];
    }
}
