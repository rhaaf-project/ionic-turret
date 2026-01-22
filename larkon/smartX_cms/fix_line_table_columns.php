<?php
// fix_line_table_columns.php
// Remove Type, Channels, Trunk columns from Line table - keep only essential columns

$file = '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php';
$content = file_get_contents($file);

// Remove Type column (lines 75-77)
$typeCol = "Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(\$state) => Line::getTypes()[\$state] ?? \$state),";
$content = str_replace($typeCol, "", $content);

// Remove channel_count column (lines 78-79)
$channelCol = "Tables\Columns\TextColumn::make('channel_count')
                    ->label('Channels'),";
$content = str_replace($channelCol, "", $content);

// Remove trunk column (lines 80-82)
$trunkCol = "Tables\Columns\TextColumn::make('trunk.name')
                    ->label('Trunk')
                    ->placeholder('-'),";
$content = str_replace($trunkCol, "", $content);

// Remove Type filter too
$typeFilter = "Tables\Filters\SelectFilter::make('type')
                    ->options(Line::getTypes()),";
$content = str_replace($typeFilter, "", $content);

// Clean up any double empty lines
$content = preg_replace("/\n\s*\n\s*\n/", "\n\n", $content);

file_put_contents($file, $content);
echo "Removed Type, Channels, Trunk columns from Line table\n";
echo "Done!\n";
