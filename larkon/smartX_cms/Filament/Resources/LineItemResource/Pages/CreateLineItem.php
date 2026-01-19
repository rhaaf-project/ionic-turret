<?php

namespace App\Filament\Resources\LineItemResource\Pages;

use App\Filament\Resources\LineItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLineItem extends CreateRecord
{
    protected static string $resource = LineItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
