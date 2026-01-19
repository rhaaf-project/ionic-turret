<?php

namespace App\Filament\Resources\CasResource\Pages;

use App\Filament\Resources\CasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCas extends EditRecord
{
    protected static string $resource = CasResource::class;

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
