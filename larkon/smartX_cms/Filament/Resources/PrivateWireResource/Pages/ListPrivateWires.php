<?php
namespace App\Filament\Resources\PrivateWireResource\Pages;

use App\Filament\Resources\PrivateWireResource;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\PrivateWireStats;
use Filament\Resources\Pages\ListRecords;

class ListPrivateWires extends ListRecords
{
    protected static string $resource = PrivateWireResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            PrivateWireStats::class,
        ];
    }
}
