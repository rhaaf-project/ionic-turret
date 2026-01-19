<?php

namespace App\Filament\Resources\LineItemResource\Pages;

use App\Filament\Resources\LineItemResource;
use Filament\Resources\Pages\Page;

class ListLineItems extends Page
{
    protected static string $resource = LineItemResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Lines';
}
