<?php

namespace App\Filament\Resources\VpwResource\Pages;

use App\Filament\Resources\VpwResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVpw extends EditRecord
{
    protected static string $resource = VpwResource::class;

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
