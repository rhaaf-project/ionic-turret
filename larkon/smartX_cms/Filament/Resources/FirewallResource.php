<?php
// Firewall Resource - Network group
namespace App\Filament\Resources;

use App\Models\Firewall;
use App\Models\CallServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FirewallResource extends Resource
{
    protected static ?string $model = Firewall::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Network';
    protected static ?string $navigationLabel = 'Firewall';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Firewall Rule Configuration')
                ->schema([
                    Forms\Components\Select::make('call_server_id')
                        ->label('Call Server')
                        ->options(CallServer::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('port')
                        ->label('Port')
                        ->placeholder('e.g., 8760, 5060')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('source')
                        ->label('Source')
                        ->placeholder('e.g., any, 10.33.42.80')
                        ->default('any')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('destination')
                        ->label('Destination')
                        ->placeholder('e.g., any')
                        ->default('any')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('protocol')
                        ->label('Protocol')
                        ->options([
                            'UDP' => 'UDP',
                            'TCP' => 'TCP',
                            'UDP, TCP' => 'UDP, TCP',
                            'ICMP' => 'ICMP',
                            'ALL' => 'ALL',
                        ])
                        ->required(),
                    Forms\Components\Select::make('interface')
                        ->label('Interface')
                        ->options([
                            'eth1' => 'Eth1',
                            'eth2' => 'Eth2',
                            'eth1, eth2' => 'Eth1, Eth2',
                        ])
                        ->required(),
                    Forms\Components\Select::make('direction')
                        ->label('Direction')
                        ->options([
                            'inbound' => 'Inbound',
                            'outbound' => 'Outbound',
                            'both' => 'Both',
                        ])
                        ->required(),
                    Forms\Components\Select::make('action')
                        ->label('Action')
                        ->options([
                            'allow' => 'Allow',
                            'block' => 'Block',
                            'drop' => 'Drop',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('comment')
                        ->label('Comment')
                        ->maxLength(255)
                        ->columnSpan(2),
                    Forms\Components\Toggle::make('is_enabled')
                        ->label('Enabled')
                        ->default(true)
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
                Tables\Columns\TextColumn::make('port')
                    ->label('Port')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->sortable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination')
                    ->sortable(),
                Tables\Columns\TextColumn::make('protocol')
                    ->label('Protocol')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('interface')
                    ->label('Interface')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'inbound' => 'info',
                        'outbound' => 'warning',
                        'both' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'allow' => 'success',
                        'block' => 'danger',
                        'drop' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->options(CallServer::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'allow' => 'Allow',
                        'block' => 'Block',
                        'drop' => 'Drop',
                    ]),
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Enabled'),
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
            'index' => \App\Filament\Resources\FirewallResource\Pages\ListFirewalls::route('/'),
            'create' => \App\Filament\Resources\FirewallResource\Pages\CreateFirewall::route('/create'),
            'edit' => \App\Filament\Resources\FirewallResource\Pages\EditFirewall::route('/{record}/edit'),
        ];
    }
}

