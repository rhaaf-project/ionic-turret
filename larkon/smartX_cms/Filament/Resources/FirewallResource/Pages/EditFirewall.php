<?php

namespace App\Filament\Resources\FirewallResource\Pages;

use App\Filament\Resources\FirewallResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFirewall extends EditRecord
{
    protected static string $resource = FirewallResource::class;

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
