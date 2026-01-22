<?php

namespace App\Filament\Widgets;

use App\Models\HeadOffice;
use App\Models\Branch;
use App\Models\CallServer;
use App\Models\User;
use App\Models\Line;
use App\Models\Extension;
use App\Models\Vpw;
use App\Models\Cas;
use App\Models\Trunk;
use App\Models\Sbc;
use App\Models\PrivateWire;
use App\Models\Intercom;
use App\Models\InboundRoute;
use App\Models\OutboundRoute;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.stats-overview';

    protected int|string|array $columnSpan = 'full';

    public function getStats(): array
    {
        return [
            [
                'label' => 'HO',
                'active' => HeadOffice::where('is_active', true)->count(),
                'inactive' => HeadOffice::where('is_active', false)->count(),
            ],
            [
                'label' => 'Branch',
                'active' => Branch::where('is_active', true)->count(),
                'inactive' => Branch::where('is_active', false)->count(),
            ],
            [
                'label' => 'Call Server',
                'active' => CallServer::where('is_active', true)->count(),
                'inactive' => CallServer::where('is_active', false)->count(),
            ],
            [
                'label' => 'Users',
                'active' => User::count(),
                'inactive' => 0,
            ],
            [
                'label' => 'Line',
                'active' => Line::where('is_active', true)->count(),
                'inactive' => Line::where('is_active', false)->count(),
            ],
            [
                'label' => 'Extension',
                'active' => Extension::count(),
                'inactive' => 0,
            ],
            [
                'label' => 'VPW',
                'active' => Vpw::where('is_active', true)->count(),
                'inactive' => Vpw::where('is_active', false)->count(),
            ],
            [
                'label' => 'CAS',
                'active' => Cas::where('is_active', true)->count(),
                'inactive' => Cas::where('is_active', false)->count(),
            ],
            [
                'label' => 'Trunk',
                'active' => Trunk::where('disabled', false)->count(),
                'inactive' => Trunk::where('disabled', true)->count(),
            ],
            [
                'label' => 'SBC',
                'active' => Sbc::where('disabled', false)->count(),
                'inactive' => Sbc::where('disabled', true)->count(),
            ],
            [
                'label' => 'Private Wire',
                'active' => PrivateWire::where('is_active', true)->count(),
                'inactive' => PrivateWire::where('is_active', false)->count(),
            ],
            [
                'label' => 'Intercom',
                'active' => Intercom::where('is_active', true)->count(),
                'inactive' => Intercom::where('is_active', false)->count(),
            ],
            [
                'label' => 'Inbound Route',
                'active' => InboundRoute::count(),
                'inactive' => 0,
            ],
            [
                'label' => 'Outbound Route',
                'active' => OutboundRoute::count(),
                'inactive' => 0,
            ],
            [
                'label' => '3rd Party',
                'active' => 0, // Placeholder - no model yet
                'inactive' => 0,
            ],
        ];
    }
}
