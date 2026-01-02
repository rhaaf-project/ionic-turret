<?php

namespace App\Filament\Resources\TurretUserResource\Pages;

use App\Filament\Resources\TurretUserResource;
use Filament\Resources\Pages\ListRecords;

class ListTurretUsers extends ListRecords
{
    protected static string $resource = TurretUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
