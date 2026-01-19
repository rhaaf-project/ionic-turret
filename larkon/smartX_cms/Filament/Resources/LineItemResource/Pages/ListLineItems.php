<?php

namespace App\Filament\Resources\LineItemResource\Pages;

use App\Filament\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineItems extends ListRecords
{
    protected static string $resource = LineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
