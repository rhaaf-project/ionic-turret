<?php
// SBC Parent Menu Resource
namespace App\Filament\Resources;

use App\Models\Sbc;
use Filament\Resources\Resource;

class SbcMenuResource extends Resource
{
    protected static ?string $model = Sbc::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'SBC ▾';
    protected static ?int $navigationSort = 10;

    // This is a parent menu only, no pages
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [];
    }
}
