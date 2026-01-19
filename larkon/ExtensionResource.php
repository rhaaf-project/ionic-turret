<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtensionResource\Pages;
use App\Models\Extension;
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

    protected static ?string $navigationParentItem = 'Line';

    protected static ?int $navigationSort = 2;

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
                        Forms\Components\Select::make('type')
                            ->label('Extension Type')
                            ->options(Extension::getTypes())
                            ->default('webrtc')
                            ->required()
                            ->helperText('WebRTC for browsers, Softphone for Zoiper/Phoner'),
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
                            ->default('internal'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'webrtc' => 'info',
                        'softphone' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'webrtc' => 'WebRTC',
                        'softphone' => 'Softphone',
                        default => $state,
                    }),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll('10s')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active status'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Extension Type')
                    ->options(Extension::getTypes()),
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
