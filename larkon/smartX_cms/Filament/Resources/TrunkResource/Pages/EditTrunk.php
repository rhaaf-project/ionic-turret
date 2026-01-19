<?php

namespace App\Filament\Resources\TrunkResource\Pages;

use App\Filament\Resources\TrunkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrunk extends EditRecord
{
    protected static string $resource = TrunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
