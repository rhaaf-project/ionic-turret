<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutboundRoutingResource\Pages;
use Filament\Resources\Resource;

class OutboundRoutingResource extends Resource
{
    protected static ?string $slug = 'outbound-routings';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationParentItem = 'Call Routing';
    protected static ?string $navigationLabel = 'Outbound';
    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutboundRoutings::route('/'),
        ];
    }
}
