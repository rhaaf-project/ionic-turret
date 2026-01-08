<?php
// System Log Resource - Log Group
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemLogResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Log';
    protected static ?string $navigationLabel = 'System Log';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('coming_soon')->content('System Log coming soon')
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
            'index' => \App\Filament\Resources\SystemLogResource\Pages\ListSystemLogs::route('/'),
        ];
    }
}
