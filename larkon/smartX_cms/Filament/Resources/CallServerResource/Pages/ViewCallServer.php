<?php

namespace App\Filament\Resources\CallServerResource\Pages;

use App\Filament\Resources\CallServerResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class ViewCallServer extends ViewRecord
{
    protected static string $resource = CallServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Server Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Server Name')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('host')
                            ->label('Host / IP')
                            ->copyable(),
                        TextEntry::make('port')
                            ->label('Port'),
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->placeholder('No description'),
                    ])->columns(4),

                Section::make('Connected Entities')
                    ->description('Resources connected to this Call Server')
                    ->schema([
                        Grid::make(7)
                            ->schema([
                                TextEntry::make('extensions_count')
                                    ->label('Extensions')
                                    ->state(fn($record) => $record->extensions()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Blue)
                                    ->icon('heroicon-o-phone'),

                                TextEntry::make('lines_count')
                                    ->label('Lines')
                                    ->state(fn($record) => $record->lines()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Green)
                                    ->icon('heroicon-o-queue-list'),

                                TextEntry::make('vpws_count')
                                    ->label('VPW')
                                    ->state(fn($record) => $record->vpws()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Purple)
                                    ->icon('heroicon-o-signal'),

                                TextEntry::make('cas_count')
                                    ->label('CAS')
                                    ->state(fn($record) => $record->cas()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Orange)
                                    ->icon('heroicon-o-server-stack'),

                                TextEntry::make('trunks_count')
                                    ->label('Trunks')
                                    ->state(fn($record) => $record->trunks()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Yellow)
                                    ->icon('heroicon-o-arrows-right-left'),

                                TextEntry::make('inbound_count')
                                    ->label('Inbound')
                                    ->state(fn($record) => $record->inboundRoutes()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Teal)
                                    ->icon('heroicon-o-arrow-down-on-square'),

                                TextEntry::make('outbound_count')
                                    ->label('Outbound')
                                    ->state(fn($record) => $record->outboundRoutes()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color(Color::Red)
                                    ->icon('heroicon-o-arrow-up-on-square'),
                            ]),
                    ]),

                Section::make('Recent Activity')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}
