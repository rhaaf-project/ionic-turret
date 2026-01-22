<?php
// Dev Turret Recording Resource - Recording Group
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DevTurretRecordingResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationGroup = 'Recording';
    protected static ?string $navigationLabel = 'Dev Turret';
    protected static ?int $navigationSort = 2;

    // Hide from navigation
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('coming_soon')->content('Dev Turret Recording management coming soon')
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
            'index' => \App\Filament\Resources\DevTurretRecordingResource\Pages\ListDevTurretRecordings::route('/'),
        ];
    }
}
