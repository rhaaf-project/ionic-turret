<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Company';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. PT Bank ABC'),
                        Forms\Components\TextInput::make('code')
                            ->label('Company Code')
                            ->maxLength(20)
                            ->placeholder('e.g. BABC')
                            ->helperText('Unique identifier code'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Contact Person')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('head_offices_count')
                    ->label('HO')
                    ->counts('headOffices'),
                Tables\Columns\TextColumn::make('branches_count')
                    ->label('Branches')
                    ->counts('branches'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Company Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Company Name')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('code')
                            ->label('Code')
                            ->badge(),
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                        TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ])->columns(3),

                InfoSection::make('Organization Structure')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('head_offices_count')
                                    ->label('Head Offices')
                                    ->state(fn($record) => $record->headOffices()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Blue)
                                    ->icon('heroicon-o-building-office-2'),
                                TextEntry::make('branches_count')
                                    ->label('Branches')
                                    ->state(fn($record) => $record->branches()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Green)
                                    ->icon('heroicon-o-map-pin'),
                            ]),
                    ]),

                InfoSection::make('Contact')
                    ->schema([
                        TextEntry::make('contact_name')
                            ->label('Contact Person'),
                        TextEntry::make('contact_email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('contact_phone')
                            ->label('Phone')
                            ->copyable(),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
