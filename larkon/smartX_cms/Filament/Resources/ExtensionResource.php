<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtensionResource\Pages;
use App\Models\Extension;
use App\Models\User;
use App\Services\AsteriskService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Connectivity';

    /**
     * Get registration status from Asterisk (cached for 10 seconds)
     */
    protected static function getRegistrationStatus(): array
    {
        return Cache::remember('asterisk_endpoints', 10, function () {
            try {
                $asterisk = app(AsteriskService::class);
                $output = $asterisk->getEndpoints();

                $status = [];
                // Parse output: "Endpoint:  6011    Not in use" or "Endpoint:  6010    Unavailable"
                preg_match_all('/Endpoint:\s+(\d+)\s+(\w+(?:\s+\w+)?)/m', $output, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $ext = $match[1];
                    $state = trim($match[2]);
                    $status[$ext] = $state;
                }

                return $status;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Extension Details')
                    ->schema([
                        Forms\Components\TextInput::make('extension')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('e.g. 6000'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Extension display name'),
                        Forms\Components\TextInput::make('secret')
                            ->required()
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->default('Maja1234')
                            ->placeholder('SIP password'),
                        Forms\Components\TextInput::make('context')
                            ->required()
                            ->maxLength(255)
                            ->default('from-internal'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Assigned User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select user (optional)')
                            ->helperText('User who will login with this extension'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $status = static::getRegistrationStatus();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('extension')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('registration_status')
                    ->label('SIP Status')
                    ->badge()
                    ->getStateUsing(fn($record) => $status[$record->extension] ?? 'Unknown')
                    ->color(fn(string $state): string => match (true) {
                        str_contains($state, 'Not in use') => 'success',
                        str_contains($state, 'In use') => 'warning',
                        str_contains($state, 'Unavailable') => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('context')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned User')
                    ->placeholder('Unassigned')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll('10s') // Auto-refresh every 10 seconds
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active status'),
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
            'index' => Pages\ListExtensions::route('/'),
            'create' => Pages\CreateExtension::route('/create'),
            'edit' => Pages\EditExtension::route('/{record}/edit'),
        ];
    }
}
