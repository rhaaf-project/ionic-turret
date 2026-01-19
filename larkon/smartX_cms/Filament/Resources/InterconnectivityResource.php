<?php

namespace App\Filament\Resources;

use App\Models\HeadOffice;
use App\Models\Branch;
use Filament\Resources\Resource;
use Filament\Pages\Page;

class InterconnectivityResource extends Resource
{
    protected static ?string $model = HeadOffice::class;

    protected static ?string $slug = 'interconnectivity';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Connectivity';

    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\InterconnectivityResource\Pages\TopologyMap::route('/'),
        ];
    }

    /**
     * Get topology data for visualization
     */
    public static function getTopologyData(): array
    {
        $nodes = [];
        $edges = [];

        // Get all head offices with branches
        $headOffices = HeadOffice::with(['branches', 'customer'])->where('is_active', true)->get();

        foreach ($headOffices as $ho) {
            // Add head office node
            $nodes[] = [
                'id' => 'ho_' . $ho->id,
                'label' => $ho->name,
                'title' => "HO: {$ho->name}\nType: {$ho->type}\nCompany: " . ($ho->customer->name ?? 'N/A'),
                'group' => 'headoffice',
                'branchCount' => $ho->branches->count(),
            ];

            // Add branch nodes and edges
            foreach ($ho->branches as $branch) {
                $nodeId = 'br_' . $branch->id;

                // Get call server IP if available
                $ip = $branch->callServer->host ?? 'N/A';
                $status = $branch->is_active ? 'OK' : 'Offline';

                $nodes[] = [
                    'id' => $nodeId,
                    'label' => $branch->name,
                    'title' => "{$branch->name}\nType: peer\nIP: {$ip}\nStatus: {$status}",
                    'group' => 'branch',
                    'status' => $status,
                    'ip' => $ip,
                ];

                // Add edge from head office to branch
                $edges[] = [
                    'from' => 'ho_' . $ho->id,
                    'to' => $nodeId,
                    'label' => '2', // Connection count placeholder
                    'color' => $branch->is_active ? '#22c55e' : '#ef4444',
                ];
            }
        }

        // Also get orphan branches (no head office)
        $orphanBranches = Branch::whereNull('head_office_id')->where('is_active', true)->get();
        foreach ($orphanBranches as $branch) {
            $ip = $branch->callServer->host ?? 'N/A';
            $nodes[] = [
                'id' => 'br_' . $branch->id,
                'label' => $branch->name,
                'title' => "{$branch->name}\nType: standalone\nIP: {$ip}",
                'group' => 'standalone',
                'ip' => $ip,
            ];
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }
}
