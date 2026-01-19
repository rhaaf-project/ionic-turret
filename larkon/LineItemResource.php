<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineItemResource\Pages;
use Filament\Resources\Resource;

class LineItemResource extends Resource
{
    protected static ?string $slug = 'line-items';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Line';
    protected static ?string $navigationLabel = 'Line';
    protected static ?int $navigationSort = 1; // First in the submenu

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLineItems::route('/'),
        ];
    }
}
