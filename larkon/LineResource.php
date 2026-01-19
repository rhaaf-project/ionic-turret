<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineResource\Pages;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;

class LineResource extends Resource
{
    protected static ?string $slug = 'lines';
    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'Line ▾';
    protected static ?int $navigationSort = 2;

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->sort(static::getNavigationSort())
                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.lines.*'))
                ->url(static::getUrl())
                ->childItems([
                    NavigationItem::make('Line')
                        ->icon('heroicon-o-queue-list')
                        ->url(route('filament.admin.resources.line-items.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.line-items.*')),
                    NavigationItem::make('Extension')
                        ->icon('heroicon-o-phone')
                        ->url(route('filament.admin.resources.extensions.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.extensions.*')),
                    NavigationItem::make('VPW')
                        ->icon('heroicon-o-signal')
                        ->url(route('filament.admin.resources.vpws.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.vpws.*')),
                    NavigationItem::make('CAS')
                        ->icon('heroicon-o-server-stack')
                        ->url(route('filament.admin.resources.cass.index'))
                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.cass.*')),
                ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLines::route('/'),
        ];
    }
}
