<?php
// fix_remove_type_column.php
// Remove Type column from LineItemResource table since Line is always webrtc

$file = '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php';
$content = file_get_contents($file);

// Remove lines 74-76 (Type column)
$old = "Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(\$state) => Line::getTypes()[\$state] ?? \$state),";

$new = "// Type column removed - Line is always webrtc";

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);

echo "Removed Type column from LineItemResource\n";

// Also remove Type filter (line 90)
$content = file_get_contents($file);
$oldFilter = "Tables\Filters\SelectFilter::make('type')
                    ->options(Line::getTypes()),";
$content = str_replace($oldFilter, "// Type filter removed", $content);
file_put_contents($file, $content);

echo "Removed Type filter from LineItemResource\n";
echo "Done!\n";
