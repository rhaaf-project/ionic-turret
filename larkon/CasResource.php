<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CasResource\Pages;
use Filament\Resources\Resource;

class CasResource extends Resource
{
    protected static ?string $slug = 'cass';
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Line';
    protected static ?string $navigationLabel = 'CAS';
    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCass::route('/'),
        ];
    }
}
