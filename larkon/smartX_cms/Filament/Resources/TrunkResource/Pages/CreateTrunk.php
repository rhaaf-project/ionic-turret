<?php

namespace App\Filament\Resources\TrunkResource\Pages;

use App\Filament\Resources\TrunkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrunk extends CreateRecord
{
    protected static string $resource = TrunkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
