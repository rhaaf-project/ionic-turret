<?php

namespace App\Filament\Resources\CallServerResource\Pages;

use App\Filament\Resources\CallServerResource;
use Filament\Resources\Pages\Page;

class ListCallServers extends Page
{
    protected static string $resource = CallServerResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Call Server / PBX';
}
