<?php

namespace App\Filament\Resources\RecordingServerResource\Pages;

use App\Filament\Resources\RecordingServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecordingServer extends EditRecord
{
    protected static string $resource = RecordingServerResource::class;

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
