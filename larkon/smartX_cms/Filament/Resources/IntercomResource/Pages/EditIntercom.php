<?php

namespace App\Filament\Resources\IntercomResource\Pages;

use App\Filament\Resources\IntercomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntercom extends EditRecord
{
    protected static string $resource = IntercomResource::class;

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
