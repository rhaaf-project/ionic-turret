<?php
namespace App\Filament\Resources\SbcRoutingResource\Pages;

use App\Filament\Resources\SbcRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSbcRoutings extends ListRecords
{
    protected static string $resource = SbcRoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
