<?php
// Recording Server Resource - Recording Group
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecordingServerResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Recording';
    protected static ?string $navigationLabel = 'Rec Server';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('coming_soon')->content('Recording Server management coming soon')
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
            'index' => \App\Filament\Resources\RecordingServerResource\Pages\ListRecordingServers::route('/'),
        ];
    }
}
