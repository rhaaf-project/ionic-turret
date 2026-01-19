<?php

namespace App\Filament\Resources\LineResource\Pages;

use App\Filament\Resources\LineResource;
use Filament\Resources\Pages\Page;

class ListLines extends Page
{
    protected static string $resource = LineResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Lines';
}
