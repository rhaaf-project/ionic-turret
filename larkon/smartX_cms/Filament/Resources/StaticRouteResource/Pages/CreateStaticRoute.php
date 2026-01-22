<?php

namespace App\Filament\Resources\StaticRouteResource\Pages;

use App\Filament\Resources\StaticRouteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaticRoute extends CreateRecord
{
    protected static string $resource = StaticRouteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
