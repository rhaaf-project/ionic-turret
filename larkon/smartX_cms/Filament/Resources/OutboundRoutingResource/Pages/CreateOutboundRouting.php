<?php

namespace App\Filament\Resources\OutboundRoutingResource\Pages;

use App\Filament\Resources\OutboundRoutingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOutboundRouting extends CreateRecord
{
    protected static string $resource = OutboundRoutingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
