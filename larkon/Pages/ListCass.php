<?php

namespace App\Filament\Resources\CasResource\Pages;

use App\Filament\Resources\CasResource;
use Filament\Resources\Pages\Page;

class ListCass extends Page
{
    protected static string $resource = CasResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'CAS';
}
