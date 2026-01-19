<?php

namespace App\Filament\Resources\HeadOfficeResource\Pages;

use App\Filament\Resources\HeadOfficeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeadOffice extends CreateRecord
{
    protected static string $resource = HeadOfficeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
