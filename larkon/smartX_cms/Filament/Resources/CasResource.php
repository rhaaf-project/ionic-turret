<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CasResource\Pages;
use App\Models\Cas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CasResource extends Resource
{
    protected static ?string $model = Cas::class;

    protected static ?string $slug = 'cass';

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Line ▾';

    protected static ?string $navigationLabel = 'CAS';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CAS Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('cas_number')
                            ->label('CAS')
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
                Tables\Columns\TextColumn::make('cas_number')
                    ->label('CAS')
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
            'index' => Pages\ListCass::route('/'),
            'create' => Pages\CreateCas::route('/create'),
            'edit' => Pages\EditCas::route('/{record}/edit'),
        ];
    }
}
