<?php
// SBC Parent Resource - Connectivity Group (with sub: Connections, Routing)
namespace App\Filament\Resources;

use App\Filament\Resources\SbcParentResource\Pages;
use App\Models\Sbc;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;

class SbcParentResource extends Resource
{
    protected static ?string $model = Sbc::class;
    protected static ?string $slug = 'sbc';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Connectivity';
    protected static ?string $navigationLabel = 'SBC ▾';
    protected static ?string $modelLabel = 'SBC';
    protected static ?string $pluralModelLabel = 'SBC';
    protected static ?int $navigationSort = 4;  // After Trunk (which is 3)

    public static function table(Table $table): Table
    {
        // Empty table - dashboard widgets will be added later
        return $table->columns([])->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSbcParents::route('/'),
        ];
    }
}
