<?php

namespace App\Filament\Resources\RecordingServerResource\Pages;

use App\Filament\Resources\RecordingServerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecordingServer extends CreateRecord
{
    protected static string $resource = RecordingServerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
