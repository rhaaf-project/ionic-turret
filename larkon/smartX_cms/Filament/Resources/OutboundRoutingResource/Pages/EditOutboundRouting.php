<?php

namespace App\Filament\Resources\OutboundRoutingResource\Pages;

use App\Filament\Resources\OutboundRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutboundRouting extends EditRecord
{
    protected static string $resource = OutboundRoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
