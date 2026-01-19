<?php

namespace App\Filament\Resources\InboundRoutingResource\Pages;

use App\Filament\Resources\InboundRoutingResource;
use Filament\Resources\Pages\Page;

class ListInboundRoutings extends Page
{
    protected static string $resource = InboundRoutingResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Inbound Routing';
}
