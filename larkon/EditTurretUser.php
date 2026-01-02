<?php

namespace App\Filament\Resources\TurretUserResource\Pages;

use App\Filament\Resources\TurretUserResource;
use Filament\Resources\Pages\EditRecord;

class EditTurretUser extends EditRecord
{
    protected static string $resource = TurretUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
