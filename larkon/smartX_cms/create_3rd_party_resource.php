<?php
// Create ThirdPartyDevice model and update resource based on Extension

$basePath = '/var/www/html/smartX';

// 1. Create ThirdPartyDevice Model
$modelContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThirdPartyDevice extends Model
{
    protected $table = 'third_party_devices';
    
    protected $fillable = [
        'call_server_id',
        'extension',
        'name',
        'secret',
        'description',
        'type',
    ];

    public function callServer(): BelongsTo
    {
        return $this->belongsTo(CallServer::class);
    }
}
PHP;

file_put_contents("$basePath/app/Models/ThirdPartyDevice.php", $modelContent);
echo "Created ThirdPartyDevice Model\n";

// 2. Create ThirdPartyDeviceResource similar to ExtensionResource
$resourceContent = <<<'PHP'
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThirdPartyDeviceResource\Pages;
use App\Models\ThirdPartyDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThirdPartyDeviceResource extends Resource
{
    protected static ?string $model = ThirdPartyDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    
    protected static ?string $navigationLabel = '3rd Party';
    
    protected static ?string $navigationGroup = 'Connectivity';
    
    protected static ?string $modelLabel = '3rd Party Device';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('call_server_id')
                    ->label('Call Server')
                    ->relationship('callServer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('extension')
                    ->label('Extension')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->label('Secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Hidden::make('type')
                    ->default('softphone'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callServer.name')
                    ->label('Call Server')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('extension')
                    ->label('Extension')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThirdPartyDevices::route('/'),
            'create' => Pages\CreateThirdPartyDevice::route('/create'),
            'edit' => Pages\EditThirdPartyDevice::route('/{record}/edit'),
        ];
    }
}
PHP;

// Create resource directory if not exists
if (!is_dir("$basePath/app/Filament/Resources/ThirdPartyDeviceResource")) {
    mkdir("$basePath/app/Filament/Resources/ThirdPartyDeviceResource", 0755, true);
}
if (!is_dir("$basePath/app/Filament/Resources/ThirdPartyDeviceResource/Pages")) {
    mkdir("$basePath/app/Filament/Resources/ThirdPartyDeviceResource/Pages", 0755, true);
}

file_put_contents("$basePath/app/Filament/Resources/ThirdPartyDeviceResource.php", $resourceContent);
echo "Created ThirdPartyDeviceResource\n";

// 3. Create Pages
$listPage = <<<'PHP'
<?php

namespace App\Filament\Resources\ThirdPartyDeviceResource\Pages;

use App\Filament\Resources\ThirdPartyDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThirdPartyDevices extends ListRecords
{
    protected static string $resource = ThirdPartyDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
PHP;

$createPage = <<<'PHP'
<?php

namespace App\Filament\Resources\ThirdPartyDeviceResource\Pages;

use App\Filament\Resources\ThirdPartyDeviceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateThirdPartyDevice extends CreateRecord
{
    protected static string $resource = ThirdPartyDeviceResource::class;
}
PHP;

$editPage = <<<'PHP'
<?php

namespace App\Filament\Resources\ThirdPartyDeviceResource\Pages;

use App\Filament\Resources\ThirdPartyDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThirdPartyDevice extends EditRecord
{
    protected static string $resource = ThirdPartyDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
PHP;

file_put_contents("$basePath/app/Filament/Resources/ThirdPartyDeviceResource/Pages/ListThirdPartyDevices.php", $listPage);
file_put_contents("$basePath/app/Filament/Resources/ThirdPartyDeviceResource/Pages/CreateThirdPartyDevice.php", $createPage);
file_put_contents("$basePath/app/Filament/Resources/ThirdPartyDeviceResource/Pages/EditThirdPartyDevice.php", $editPage);
echo "Created ThirdPartyDevice Pages\n";

echo "\nDone! Run: php artisan optimize:clear\n";
