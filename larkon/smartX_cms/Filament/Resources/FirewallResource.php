<?php
// Firewall Resource - Network group
namespace App\Filament\Resources;

use App\Models\CallServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FirewallResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Network';
    protected static ?string $navigationLabel = 'Firewall';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('call_server_id')
                ->label('Call Server')
                ->options(CallServer::pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\Placeholder::make('coming_soon')->content('Firewall management coming soon')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Coming Soon'),
        ])->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FirewallResource\Pages\ListFirewalls::route('/'),
        ];
    }
}
