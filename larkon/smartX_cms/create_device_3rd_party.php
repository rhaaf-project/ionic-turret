<?php
// Create 3rd Party resource under Device menu (separate from Line's 3rd Party)

$basePath = '/var/www/html/smartX';

// 1. Create Device3rdPartyResource
$resourceContent = <<<'PHP'
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Device3rdPartyResource\Pages;
use App\Models\Device3rdParty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class Device3rdPartyResource extends Resource
{
    protected static ?string $model = Device3rdParty::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    
    protected static ?string $navigationLabel = '3rd Party';
    
    protected static ?string $navigationGroup = 'Device';
    
    protected static ?string $modelLabel = '3rd Party Device';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Device Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mac_address')
                    ->label('MAC Address')
                    ->maxLength(17)
                    ->placeholder('XX:XX:XX:XX:XX:XX'),
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Address')
                    ->maxLength(15),
                Forms\Components\Select::make('device_type')
                    ->label('Device Type')
                    ->options([
                        'ip_phone' => 'IP Phone',
                        'softphone' => 'Softphone',
                        'gateway' => 'Gateway',
                        'other' => 'Other',
                    ])
                    ->default('ip_phone'),
                Forms\Components\TextInput::make('manufacturer')
                    ->label('Manufacturer')
                    ->maxLength(100),
                Forms\Components\TextInput::make('model')
                    ->label('Model')
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Device Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('manufacturer')
                    ->label('Manufacturer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
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
            'index' => Pages\ListDevice3rdParties::route('/'),
            'create' => Pages\CreateDevice3rdParty::route('/create'),
            'edit' => Pages\EditDevice3rdParty::route('/{record}/edit'),
        ];
    }
}
PHP;

// Create resource directory
if (!is_dir("$basePath/app/Filament/Resources/Device3rdPartyResource")) {
    mkdir("$basePath/app/Filament/Resources/Device3rdPartyResource", 0755, true);
}
if (!is_dir("$basePath/app/Filament/Resources/Device3rdPartyResource/Pages")) {
    mkdir("$basePath/app/Filament/Resources/Device3rdPartyResource/Pages", 0755, true);
}

file_put_contents("$basePath/app/Filament/Resources/Device3rdPartyResource.php", $resourceContent);
echo "Created Device3rdPartyResource\n";

// 2. Create Pages
$listPage = <<<'PHP'
<?php

namespace App\Filament\Resources\Device3rdPartyResource\Pages;

use App\Filament\Resources\Device3rdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDevice3rdParties extends ListRecords
{
    protected static string $resource = Device3rdPartyResource::class;

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

namespace App\Filament\Resources\Device3rdPartyResource\Pages;

use App\Filament\Resources\Device3rdPartyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDevice3rdParty extends CreateRecord
{
    protected static string $resource = Device3rdPartyResource::class;
}
PHP;

$editPage = <<<'PHP'
<?php

namespace App\Filament\Resources\Device3rdPartyResource\Pages;

use App\Filament\Resources\Device3rdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDevice3rdParty extends EditRecord
{
    protected static string $resource = Device3rdPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
PHP;

file_put_contents("$basePath/app/Filament/Resources/Device3rdPartyResource/Pages/ListDevice3rdParties.php", $listPage);
file_put_contents("$basePath/app/Filament/Resources/Device3rdPartyResource/Pages/CreateDevice3rdParty.php", $createPage);
file_put_contents("$basePath/app/Filament/Resources/Device3rdPartyResource/Pages/EditDevice3rdParty.php", $editPage);
echo "Created Device3rdParty Pages\n";

// 3. Create Model
$modelContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device3rdParty extends Model
{
    protected $table = 'device_3rd_parties';
    
    protected $fillable = [
        'name',
        'mac_address',
        'ip_address',
        'device_type',
        'manufacturer',
        'model',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
PHP;

file_put_contents("$basePath/app/Models/Device3rdParty.php", $modelContent);
echo "Created Device3rdParty Model\n";

// 4. Create database table
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require "$basePath/vendor/autoload.php";
$app = require_once "$basePath/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (!Schema::hasTable('device_3rd_parties')) {
    Schema::create('device_3rd_parties', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('mac_address', 17)->nullable();
        $table->string('ip_address', 15)->nullable();
        $table->string('device_type')->default('ip_phone');
        $table->string('manufacturer', 100)->nullable();
        $table->string('model', 100)->nullable();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    echo "Created device_3rd_parties table\n";
} else {
    echo "Table device_3rd_parties already exists\n";
}

echo "\nDone!\n";
