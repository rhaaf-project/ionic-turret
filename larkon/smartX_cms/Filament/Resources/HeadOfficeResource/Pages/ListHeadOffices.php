<?php

namespace App\Filament\Resources\HeadOfficeResource\Pages;

use App\Filament\Resources\HeadOfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeadOffices extends ListRecords
{
    protected static string $resource = HeadOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
