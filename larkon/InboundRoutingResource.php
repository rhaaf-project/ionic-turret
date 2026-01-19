<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InboundRoutingResource\Pages;
use Filament\Resources\Resource;

class InboundRoutingResource extends Resource
{
    protected static ?string $slug = 'inbound-routings';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Call Routing';
    protected static ?string $navigationLabel = 'Inbound';
    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInboundRoutings::route('/'),
        ];
    }
}
