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
        return PrivateWire::all()->map(function ($pw) {
            return [
                'id' => $pw->id,
                'label' => $pw->name,
                'number' => $pw->number,
                'is_active' => $pw->is_active,
            ];
        })->toArray();
    }
}
