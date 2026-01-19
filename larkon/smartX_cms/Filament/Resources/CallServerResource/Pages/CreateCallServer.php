<?php

namespace App\Filament\Resources\CallServerResource\Pages;

use App\Filament\Resources\CallServerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCallServer extends CreateRecord
{
    protected static string $resource = CallServerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
