<?php

namespace App\Filament\Resources\VpwResource\Pages;

use App\Filament\Resources\VpwResource;
use Filament\Resources\Pages\Page;

class ListVpws extends Page
{
    protected static string $resource = VpwResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'VPW';
}
