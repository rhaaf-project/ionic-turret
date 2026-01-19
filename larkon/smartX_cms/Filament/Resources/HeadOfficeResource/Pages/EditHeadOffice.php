<?php

namespace App\Filament\Resources\HeadOfficeResource\Pages;

use App\Filament\Resources\HeadOfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeadOffice extends EditRecord
{
    protected static string $resource = HeadOfficeResource::class;

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
