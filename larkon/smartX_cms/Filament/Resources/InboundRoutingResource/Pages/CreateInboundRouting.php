<?php

namespace App\Filament\Resources\InboundRoutingResource\Pages;

use App\Filament\Resources\InboundRoutingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInboundRouting extends CreateRecord
{
    protected static string $resource = InboundRoutingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
