<?php

namespace App\Filament\Resources\TrunkResource\Pages;

use App\Filament\Resources\TrunkResource;
use Filament\Resources\Pages\Page;

class ListTrunks extends Page
{
    protected static string $resource = TrunkResource::class;
    protected static string $view = 'filament.resources.placeholder';
    protected static ?string $title = 'Trunks';
}
