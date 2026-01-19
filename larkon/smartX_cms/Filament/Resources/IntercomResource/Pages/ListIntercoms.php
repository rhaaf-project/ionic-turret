<?php

namespace App\Filament\Resources\IntercomResource\Pages;

use App\Filament\Resources\IntercomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntercoms extends ListRecords
{
    protected static string $resource = IntercomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
