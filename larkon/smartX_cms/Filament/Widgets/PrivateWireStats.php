<?php

namespace App\Filament\Widgets;

use App\Models\PrivateWire;
use Filament\Widgets\Widget;

class PrivateWireStats extends Widget
{
    protected static string $view = 'filament.widgets.private-wire-stats';

    protected int|string|array $columnSpan = 'full';

    public function getPrivateWires(): array
    {
        // Hide PW-Jakarta widgets from dashboard
        $hiddenPws = [
            'PW-Jakarta-Bandung',
            'PW-Jakarta-Surabaya',
            'PW-Jakarta-Medan',
            'PW-Jakarta-Makassar',
            'PW-Jakarta-Semarang',
        ];

        return PrivateWire::all()
            ->filter(fn($pw) => !in_array($pw->name, $hiddenPws))
            ->map(function ($pw) {
                return [
                    'id' => $pw->id,
                    'label' => $pw->name,
                    'number' => $pw->number,
                    'is_active' => $pw->is_active,
                ];
            })->values()->toArray();
    }
}
