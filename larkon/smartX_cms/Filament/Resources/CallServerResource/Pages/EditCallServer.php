<?php

namespace App\Filament\Resources\CallServerResource\Pages;

use App\Filament\Resources\CallServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCallServer extends EditRecord
{
    protected static string $resource = CallServerResource::class;

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
