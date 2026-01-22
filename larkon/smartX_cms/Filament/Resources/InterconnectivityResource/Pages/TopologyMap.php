<?php

namespace App\Filament\Resources\InterconnectivityResource\Pages;

use App\Filament\Resources\InterconnectivityResource;
use Filament\Resources\Pages\Page;

class TopologyMap extends Page
{
    protected static string $resource = InterconnectivityResource::class;

    protected static string $view = 'filament.pages.topology-map';

    protected static ?string $title = 'Connectivity Diagram';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public array $topologyData = [];

    public function mount(): void
    {
        $this->topologyData = InterconnectivityResource::getTopologyData();
    }

    public function getViewData(): array
    {
        return [
            'topologyData' => $this->topologyData,
        ];
    }
}
