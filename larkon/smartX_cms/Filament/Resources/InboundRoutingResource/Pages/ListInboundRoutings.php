<?php

namespace App\Filament\Resources\InboundRoutingResource\Pages;

use App\Filament\Resources\InboundRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInboundRoutings extends ListRecords
{
    protected static string $resource = InboundRoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
