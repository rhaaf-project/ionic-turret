<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Placeholder - User Group
class GroupResource extends Resource
{
    protected static ?string $model = \App\Models\User::class; // Temporary
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User';
    protected static ?string $navigationLabel = 'Group';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('coming_soon')
                ->content('Group management coming soon')
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
            'index' => \App\Filament\Resources\GroupResource\Pages\ListGroups::route('/'),
        ];
    }
}
