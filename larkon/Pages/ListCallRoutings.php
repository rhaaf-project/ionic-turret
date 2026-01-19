<?php

namespace App\Filament\Resources\CallRoutingResource\Pages;

use App\Filament\Resources\CallRoutingResource;
use Filament\Resources\Pages\Page;

class ListCallRoutings extends Page
{
    protected static string $resource = CallRoutingResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Call Routing';
}
