<?php
// Static Route Resource - Network group
namespace App\Filament\Resources;

use App\Models\StaticRoute;
use App\Models\CallServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaticRouteResource extends Resource
{
    protected static ?string $model = StaticRoute::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Network';
    protected static ?string $navigationLabel = 'Static Route';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Static Route Configuration')
                ->schema([
                    Forms\Components\Select::make('call_server_id')
                        ->label('Call Server')
                        ->options(CallServer::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('network')
                        ->label('Network')
                        ->placeholder('e.g., 13.44.13.0')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('subnet')
                        ->label('Subnet')
                        ->placeholder('e.g., 255.255.255.0')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('gateway')
                        ->label('Gateway')
                        ->placeholder('e.g., 13.44.13.2')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('device')
                        ->label('Device')
                        ->placeholder('e.g., Eth1')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->maxLength(255)
                        ->columnSpan(2),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callServer.name')
                    ->label('Call Server')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('network')
                    ->label('Network')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subnet')
                    ->label('Subnet')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Gateway')
                    ->sortable(),
                Tables\Columns\TextColumn::make('device')
                    ->label('Device')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->options(CallServer::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('device')
                    ->options([
                        'Eth1' => 'Eth1',
                        'Eth2' => 'Eth2',
                    ]),
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
            'index' => \App\Filament\Resources\StaticRouteResource\Pages\ListStaticRoutes::route('/'),
            'create' => \App\Filament\Resources\StaticRouteResource\Pages\CreateStaticRoute::route('/create'),
            'edit' => \App\Filament\Resources\StaticRouteResource\Pages\EditStaticRoute::route('/{record}/edit'),
        ];
    }
}

