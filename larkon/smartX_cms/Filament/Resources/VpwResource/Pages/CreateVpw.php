<?php

namespace App\Filament\Resources\VpwResource\Pages;

use App\Filament\Resources\VpwResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVpw extends CreateRecord
{
    protected static string $resource = VpwResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
