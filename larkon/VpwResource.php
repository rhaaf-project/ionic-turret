<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VpwResource\Pages;
use Filament\Resources\Resource;

class VpwResource extends Resource
{
    protected static ?string $slug = 'vpws';
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Line';
    protected static ?string $navigationLabel = 'VPW';
    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVpws::route('/'),
        ];
    }
}
