<?php

namespace App\Filament\Resources\InboundRoutingResource\Pages;

use App\Filament\Resources\InboundRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInboundRouting extends EditRecord
{
    protected static string $resource = InboundRoutingResource::class;

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
