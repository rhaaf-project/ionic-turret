<?php
namespace App\Filament\Resources\RecordingServerResource\Pages;

use App\Filament\Resources\RecordingServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecordingServers extends ListRecords
{
    protected static string $resource = RecordingServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

