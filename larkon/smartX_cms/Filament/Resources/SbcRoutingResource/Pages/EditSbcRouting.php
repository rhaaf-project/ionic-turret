<?php
namespace App\Filament\Resources\SbcRoutingResource\Pages;

use App\Filament\Resources\SbcRoutingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSbcRouting extends EditRecord
{
    protected static string $resource = SbcRoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
