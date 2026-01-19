<?php

namespace App\Filament\Resources\VpwResource\Pages;

use App\Filament\Resources\VpwResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVpws extends ListRecords
{
    protected static string $resource = VpwResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
