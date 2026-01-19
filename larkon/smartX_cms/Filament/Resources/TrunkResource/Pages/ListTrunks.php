<?php

namespace App\Filament\Resources\TrunkResource\Pages;

use App\Filament\Resources\TrunkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrunks extends ListRecords
{
    protected static string $resource = TrunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
