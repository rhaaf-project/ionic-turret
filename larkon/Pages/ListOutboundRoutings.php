<?php

namespace App\Filament\Resources\OutboundRoutingResource\Pages;

use App\Filament\Resources\OutboundRoutingResource;
use Filament\Resources\Pages\Page;

class ListOutboundRoutings extends Page
{
    protected static string $resource = OutboundRoutingResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Outbound Routing';
}
