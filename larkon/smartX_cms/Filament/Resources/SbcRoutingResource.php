<?php
// SBC Routing Resource
namespace App\Filament\Resources;

use App\Filament\Resources\SbcRoutingResource\Pages;
use App\Models\SbcRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SbcRoutingResource extends Resource
{
    protected static ?string $model = SbcRoute::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'SBC ▾';
    protected static ?string $navigationLabel = 'Routing';
    protected static ?string $modelLabel = 'SBC Routing';
    protected static ?string $pluralModelLabel = 'SBC Routing';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Routing Rule')
                    ->schema([
                        Forms\Components\Select::make('sbc_id')
                            ->label('SBC')
                            ->relationship('sbc', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('pattern')
                            ->label('Pattern')
                            ->required()
                            ->placeholder('e.g. _X. or _021XXXXXXX'),
                        Forms\Components\TextInput::make('prefix')
                            ->label('Prefix')
                            ->placeholder('Prefix to add'),
                        Forms\Components\TextInput::make('strip')
                            ->label('Strip Digits')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('priority')
                            ->label('Priority')
                            ->numeric()
                            ->default(1),
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
                Tables\Columns\TextColumn::make('sbc.name')
                    ->label('SBC')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pattern')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('strip')
                    ->label('Strip'),
                Tables\Columns\TextColumn::make('priority'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sbc_id')
                    ->label('SBC')
                    ->relationship('sbc', 'name'),
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
            'index' => Pages\ListSbcRoutings::route('/'),
            'create' => Pages\CreateSbcRouting::route('/create'),
            'edit' => Pages\EditSbcRouting::route('/{record}/edit'),
        ];
    }
}
