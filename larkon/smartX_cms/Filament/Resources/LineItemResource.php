<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineItemResource\Pages;
use App\Models\Line;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LineItemResource extends Resource
{
    protected static ?string $model = Line::class;

    protected static ?string $slug = 'line-items';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Connectivity';

    protected static ?string $navigationParentItem = 'Line ▾';

    protected static ?string $navigationLabel = 'Line';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Line Details')
                    ->schema([
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('line_number')
                            ->label('Line')
                            ->required()
                            ->minLength(7)
                            ->maxLength(50)
                            ->rules(['min:7'])
                            ->validationMessages([
                                'min' => 'Line number must be at least 7 digits',
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('secret')
                            ->label('Secret')
                            ->required()
                            ->password()
                            ->revealable()
                            ->maxLength(32),
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
                Tables\Columns\TextColumn::make('line_number')
                    ->label('Line')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('secret')
                    ->label('Secret')
                    ->copyable(),
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
            'index' => Pages\ListLineItems::route('/'),
            'create' => Pages\CreateLineItem::route('/create'),
            'edit' => Pages\EditLineItem::route('/{record}/edit'),
        ];
    }
}
