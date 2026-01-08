<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TurretUserResource\Pages;
use App\Models\User;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TurretUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Turret Management';

    protected static ?string $navigationLabel = 'Turret Users';

    protected static ?string $modelLabel = 'Turret User';

    protected static ?string $pluralModelLabel = 'Turret Users';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Username')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, $operation) {
                                if ($operation === 'create' && $state) {
                                    // Auto-generate email from username
                                    $email = Str::slug($state, '.') . '@smartx.local';
                                    $set('email', $email);
                                }
                            }),
                        Forms\Components\Hidden::make('email'),
                    ]),

                Forms\Components\Section::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText('Leave empty to keep current password'),
                    ]),

                Forms\Components\Section::make('SIP Extension')
                    ->schema([
                        Forms\Components\Select::make('use_ext')
                            ->label('Assigned Extension')
                            ->options(Extension::pluck('extension', 'extension'))
                            ->searchable()
                            ->required()
                            ->helperText('SIP extension for turret login'),
                        Forms\Components\Hidden::make('role')
                            ->default('user'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('use_ext')
                    ->label('Extension')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListTurretUsers::route('/'),
            'create' => Pages\CreateTurretUser::route('/create'),
            'edit' => Pages\EditTurretUser::route('/{record}/edit'),
        ];
    }
}
