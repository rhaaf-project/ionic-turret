<?php

namespace App\Filament\Resources\CasResource\Pages;

use App\Filament\Resources\CasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCass extends ListRecords
{
    protected static string $resource = CasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
