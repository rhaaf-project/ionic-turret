<?php

namespace App\Filament\Resources\WebDeviceResource\Pages;

use App\Filament\Resources\WebDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebDevice extends EditRecord
{
    protected static string $resource = WebDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
