<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallServerResource\Pages;
use Filament\Resources\Resource;

class CallServerResource extends Resource
{
    protected static ?string $slug = 'call-servers';
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'Call Server / PBX';
    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCallServers::route('/'),
        ];
    }
}
