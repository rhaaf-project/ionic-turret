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

    protected static ?string $navigationParentItem = 'Line';

    protected static ?string $navigationLabel = 'CAS';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CAS Channel Details')
                    ->description('CAS channels are auto-dial lines')
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
                            ->placeholder('e.g. CAS Channel 01'),
                        Forms\Components\TextInput::make('channel_number')
                            ->label('Number')
                            ->numeric()
                            ->placeholder('e.g. 1'),
                        Forms\Components\Select::make('trunk_id')
                            ->label('Trunk')
                            ->relationship('trunk', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select trunk (optional)'),
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
                Tables\Columns\TextColumn::make('channel_number')
                    ->label('Number'),
                Tables\Columns\TextColumn::make('trunk.name')
                    ->label('Trunk')
                    ->placeholder('-'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCass::route('/'),
            'create' => Pages\CreateCas::route('/create'),
            'edit' => Pages\EditCas::route('/{record}/edit'),
        ];
    }
}
