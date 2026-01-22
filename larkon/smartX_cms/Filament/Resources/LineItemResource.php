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
                            ->preload()
                            ->helperText('Select which PBX this line is connected to'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Line HarInd 01'),
                        Forms\Components\TextInput::make('line_number')
                            ->label('Line Number')
                            ->maxLength(50)
                            ->placeholder('e.g. 02150877432'),
                        Forms\Components\Select::make('type')
                            ->options(Line::getTypes())
                            ->default('sip')
                            ->required(),
                        Forms\Components\TextInput::make('channel_count')
                            ->label('Channel Count')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\Select::make('trunk_id')
                            ->label('Trunk')
                            ->relationship('trunk', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select associated trunk'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
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
                Tables\Columns\TextColumn::make('line_number')
                    ->label('Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => Line::getTypes()[$state] ?? $state),
                Tables\Columns\TextColumn::make('channel_count')
                    ->label('Channels'),
                Tables\Columns\TextColumn::make('trunk.name')
                    ->label('Trunk')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Line::getTypes()),
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
