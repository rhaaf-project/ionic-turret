<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntercomResource\Pages;
use App\Models\Intercom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IntercomResource extends Resource
{
    protected static ?string $model = Intercom::class;

    protected static ?string $slug = 'intercoms';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationLabel = 'Intercom';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Intercom Details')
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
                            ->placeholder('e.g. Lobby Intercom'),
                        Forms\Components\TextInput::make('extension')
                            ->maxLength(20)
                            ->placeholder('e.g. 8001'),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),
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
                Tables\Columns\TextColumn::make('extension')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
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
            'index' => Pages\ListIntercoms::route('/'),
            'create' => Pages\CreateIntercom::route('/create'),
            'edit' => Pages\EditIntercom::route('/{record}/edit'),
        ];
    }
}
