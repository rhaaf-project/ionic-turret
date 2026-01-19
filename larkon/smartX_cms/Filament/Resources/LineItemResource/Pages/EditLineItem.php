<?php

namespace App\Filament\Resources\LineItemResource\Pages;

use App\Filament\Resources\LineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLineItem extends EditRecord
{
    protected static string $resource = LineItemResource::class;

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
