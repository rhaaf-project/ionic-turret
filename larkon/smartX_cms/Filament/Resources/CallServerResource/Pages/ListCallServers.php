<?php

namespace App\Filament\Resources\CallServerResource\Pages;

use App\Filament\Resources\CallServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCallServers extends ListRecords
{
    protected static string $resource = CallServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
