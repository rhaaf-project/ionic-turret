<?php

namespace App\Filament\Resources\CasResource\Pages;

use App\Filament\Resources\CasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCas extends CreateRecord
{
    protected static string $resource = CasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
