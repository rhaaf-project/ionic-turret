<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallRoutingResource\Pages;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;

class CallRoutingResource extends Resource
{
    protected static ?string $slug = 'call-routings';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'Call Routing ▾';
    protected static ?int $navigationSort = 4;

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->sort(static::getNavigationSort())
                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.call-routings.*'))
                ->url(static::getUrl())
                ->childItems([
                    NavigationItem::make('Inbound')
                        ->icon('heroicon-o-arrow-down-on-square')
                        ->url(route('filament.admin.resources.inbound-routings.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.inbound-routings.*')),
                    NavigationItem::make('Outbound')
                        ->icon('heroicon-o-arrow-up-on-square')
                        ->url(route('filament.admin.resources.outbound-routings.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.outbound-routings.*')),
                ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCallRoutings::route('/'),
        ];
    }
}
