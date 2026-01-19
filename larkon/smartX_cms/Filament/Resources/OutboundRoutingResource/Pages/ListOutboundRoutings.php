<?php

namespace App\Filament\Resources\OutboundRoutingResource\Pages;

use App\Filament\Resources\OutboundRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutboundRoutings extends ListRecords
{
    protected static string $resource = OutboundRoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
