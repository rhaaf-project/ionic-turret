<?php

namespace App\Filament\Resources\IntercomResource\Pages;

use App\Filament\Resources\IntercomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntercom extends CreateRecord
{
    protected static string $resource = IntercomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
