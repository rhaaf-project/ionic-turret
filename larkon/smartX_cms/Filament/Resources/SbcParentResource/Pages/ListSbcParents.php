<?php
namespace App\Filament\Resources\SbcParentResource\Pages;

use App\Filament\Resources\SbcParentResource;
use Filament\Resources\Pages\ListRecords;

class ListSbcParents extends ListRecords
{
    protected static string $resource = SbcParentResource::class;

    protected static ?string $title = 'SBC';

    public function getHeading(): string
    {
        return 'SBC';
    }
}
