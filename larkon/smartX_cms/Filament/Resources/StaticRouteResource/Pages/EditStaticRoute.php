<?php

namespace App\Filament\Resources\StaticRouteResource\Pages;

use App\Filament\Resources\StaticRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaticRoute extends EditRecord
{
    protected static string $resource = StaticRouteResource::class;

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
