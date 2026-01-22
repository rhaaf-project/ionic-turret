<?php
// Recording Server Resource - Recording Group
namespace App\Filament\Resources;

use App\Models\RecordingServer;
use App\Models\CallServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecordingServerResource extends Resource
{
    protected static ?string $model = RecordingServer::class;
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Recording';
    protected static ?string $navigationLabel = 'Rec Server';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Recording Server Configuration')
                ->schema([
                    Forms\Components\Select::make('call_server_id')
                        ->label('Call Server')
                        ->options(CallServer::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('ip_address')
                        ->label('Recording IP Address')
                        ->placeholder('e.g., 192.168.1.100')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('port')
                        ->label('Port')
                        ->placeholder('e.g., 5060')
                        ->maxLength(10),
                    Forms\Components\Select::make('pbx_system_type')
                        ->label('PBX System Type')
                        ->options(RecordingServer::getPbxSystemTypes())
                        ->searchable()
                        ->default('None')
                        ->required()
                        ->columnSpan(2),
                ])
                ->columns(2),

            Forms\Components\Section::make('PBX System Configuration')
                ->schema([
                    Forms\Components\TextInput::make('pbx_system_1')
                        ->label('PBX System 1')
                        ->placeholder('Configuration for PBX System 1')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('pbx_system_2')
                        ->label('PBX System 2')
                        ->placeholder('Configuration for PBX System 2')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('pbx_system_3')
                        ->label('PBX System 3')
                        ->placeholder('Configuration for PBX System 3')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('pbx_system_4')
                        ->label('PBX System 4')
                        ->placeholder('Configuration for PBX System 4')
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Additional Settings')
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->maxLength(255)
                        ->columnSpan(2),
                    Forms\Components\Toggle::make('is_enabled')
                        ->label('Enabled')
                        ->default(true),
                ])
                ->columns(2),
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
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Recording IP')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->label('Port')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pbx_system_type')
                    ->label('PBX Type')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pbx_system_1')
                    ->label('PBX 1')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pbx_system_2')
                    ->label('PBX 2')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pbx_system_3')
                    ->label('PBX 3')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pbx_system_4')
                    ->label('PBX 4')
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_server_id')
                    ->label('Call Server')
                    ->options(CallServer::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('pbx_system_type')
                    ->label('PBX Type')
                    ->options(RecordingServer::getPbxSystemTypes()),
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Enabled'),
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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\RecordingServerResource\Pages\ListRecordingServers::route('/'),
            'create' => \App\Filament\Resources\RecordingServerResource\Pages\CreateRecordingServer::route('/create'),
            'edit' => \App\Filament\Resources\RecordingServerResource\Pages\EditRecordingServer::route('/{record}/edit'),
        ];
    }
}

