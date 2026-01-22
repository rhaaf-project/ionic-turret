<?php

namespace App\Filament\Resources\FirewallResource\Pages;

use App\Filament\Resources\FirewallResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFirewall extends CreateRecord
{
    protected static string $resource = FirewallResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
