<?php
// fix_line_form_fields.php
// 1. Fix form field 'line' -> 'line_number' in LineItemResource
// 2. Add 'secret' to Line model fillable
// 3. Remove Type column from table

// Fix 1: Form field name
$file1 = '/var/www/html/smartX/app/Filament/Resources/LineItemResource.php';
$content = file_get_contents($file1);

// Fix form field 'line' to 'line_number'
$content = str_replace(
    "TextInput::make('line')\n                    ->label('Line')",
    "TextInput::make('line_number')\n                    ->label('Line')",
    $content
);

// Remove Type column from table
$oldTypeColumn = "Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(\$state) => Line::getTypes()[\$state] ?? \$state),";
$content = str_replace($oldTypeColumn, "// Type column removed", $content);

// Remove Type filter
$oldTypeFilter = "Tables\Filters\SelectFilter::make('type')
                    ->options(Line::getTypes()),";
$content = str_replace($oldTypeFilter, "// Type filter removed", $content);

file_put_contents($file1, $content);
echo "Fixed LineItemResource: line->line_number, removed Type column\n";

// Fix 2: Add 'secret' to Line model fillable
$file2 = '/var/www/html/smartX/app/Models/Line.php';
$content2 = file_get_contents($file2);

if (strpos($content2, "'secret'") === false) {
    $content2 = str_replace(
        "'description',",
        "'description',\n        'secret',",
        $content2
    );
    file_put_contents($file2, $content2);
    echo "Added 'secret' to Line model fillable\n";
} else {
    echo "SKIP: 'secret' already in Line model\n";
}

echo "\nDone!\n";
