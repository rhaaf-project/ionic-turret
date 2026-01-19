<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrivateWireResource\Pages;
use App\Models\PrivateWire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrivateWireResource extends Resource
{
    protected static ?string $model = PrivateWire::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationLabel = 'Private Wire';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Private Wire Details')
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
                            ->placeholder('e.g. PW-Jakarta-Bandung'),
                        Forms\Components\TextInput::make('number')
                            ->label('Number')
                            ->maxLength(50)
                            ->placeholder('e.g. 9001'),
                        Forms\Components\TextInput::make('destination')
                            ->label('Destination')
                            ->maxLength(100)
                            ->placeholder('e.g. 10.1.0.10'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('In Use')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Green = In Use, Red = Not In Use'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(2),
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
                Tables\Columns\TextColumn::make('number')
                    ->label('Number'),
                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('In Use')
                    ->falseLabel('Not In Use')
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
            'index' => Pages\ListPrivateWires::route('/'),
            'create' => Pages\CreatePrivateWire::route('/create'),
            'edit' => Pages\EditPrivateWire::route('/{record}/edit'),
        ];
    }
}
