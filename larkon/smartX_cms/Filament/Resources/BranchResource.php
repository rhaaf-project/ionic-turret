<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Branch';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Branch Details')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Company')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\Select::make('head_office_id')
                            ->label('Head Office')
                            ->relationship(
                                'headOffice',
                                'name',
                                fn($query, $get) =>
                                $get('customer_id') ? $query->where('customer_id', $get('customer_id')) : $query
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Select HO (optional)'),
                        Forms\Components\Select::make('call_server_id')
                            ->label('Call Server')
                            ->relationship('callServer', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Cabang Sunter'),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(20)
                            ->placeholder('e.g. SUN001'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),

                Forms\Components\Section::make('Location')
                    ->description('Geographic location of this branch')
                    ->schema([
                        Forms\Components\TextInput::make('country')
                            ->default('Indonesia')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('province')
                            ->maxLength(50)
                            ->placeholder('e.g. DKI Jakarta'),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(50)
                            ->placeholder('e.g. Jakarta Utara'),
                        Forms\Components\TextInput::make('district')
                            ->maxLength(50)
                            ->placeholder('e.g. Sunter'),
                        Forms\Components\Textarea::make('address')
                            ->label('Full Address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(4),

                Forms\Components\Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Branch Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('headOffice.name')
                    ->label('HO')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('province')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->label('City'),
                Tables\Columns\TextColumn::make('district')
                    ->label('District'),
                Tables\Columns\TextColumn::make('callServer.name')
                    ->label('Call Server')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('extensions_count')
                    ->label('Ext')
                    ->counts('extensions'),
                Tables\Columns\TextColumn::make('lines_count')
                    ->label('Lines')
                    ->counts('lines'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Company')
                    ->relationship('customer', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? '-'),
                Tables\Filters\SelectFilter::make('head_office_id')
                    ->label('Head Office')
                    ->relationship('headOffice', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? '-'),
                Tables\Filters\SelectFilter::make('province')
                    ->options(fn() => Branch::query()->whereNotNull('province')->distinct()->pluck('province', 'province')->toArray()),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
