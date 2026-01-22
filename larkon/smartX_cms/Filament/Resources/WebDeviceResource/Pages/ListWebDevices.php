<?php
namespace App\Filament\Resources\WebDeviceResource\Pages;

use App\Filament\Resources\WebDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebDevices extends ListRecords
{
    protected static string $resource = WebDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
