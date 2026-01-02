<?php

namespace App\Filament\Resources\TurretUserResource\Pages;

use App\Filament\Resources\TurretUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTurretUser extends CreateRecord
{
    protected static string $resource = TurretUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'user';
        return $data;
    }
}
