<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrunkResource\Pages;
use Filament\Resources\Resource;

class TrunkResource extends Resource
{
    protected static ?string $slug = 'trunks';
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'Trunk';
    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrunks::route('/'),
        ];
    }
}
