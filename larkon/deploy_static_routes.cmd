@echo off
REM Deploy Static Route feature to server
REM Server: 103.154.80.171 (Laravel + PostgreSQL)

set SERVER=root@103.154.80.171
set REMOTE_PATH=/var/www/html
set LOCAL_PATH=%~dp0

echo === Deploying Static Route Feature ===

REM Upload Model
echo Uploading StaticRoute model...
scp "%LOCAL_PATH%smartX_cms\Models\StaticRoute.php" %SERVER%:%REMOTE_PATH%/app/Models/

REM Upload Resource
echo Uploading StaticRouteResource...
scp "%LOCAL_PATH%smartX_cms\Filament\Resources\StaticRouteResource.php" %SERVER%:%REMOTE_PATH%/app/Filament/Resources/

REM Upload Pages
echo Uploading Resource Pages...
ssh %SERVER% "mkdir -p %REMOTE_PATH%/app/Filament/Resources/StaticRouteResource/Pages"
scp "%LOCAL_PATH%smartX_cms\Filament\Resources\StaticRouteResource\Pages\ListStaticRoutes.php" %SERVER%:%REMOTE_PATH%/app/Filament/Resources/StaticRouteResource/Pages/
scp "%LOCAL_PATH%smartX_cms\Filament\Resources\StaticRouteResource\Pages\CreateStaticRoute.php" %SERVER%:%REMOTE_PATH%/app/Filament/Resources/StaticRouteResource/Pages/
scp "%LOCAL_PATH%smartX_cms\Filament\Resources\StaticRouteResource\Pages\EditStaticRoute.php" %SERVER%:%REMOTE_PATH%/app/Filament/Resources/StaticRouteResource/Pages/

REM Upload Seeder script
echo Uploading seeder script...
scp "%LOCAL_PATH%smartX_cms\seed_static_routes.php" %SERVER%:%REMOTE_PATH%/

REM Run seeder on server
echo Running seeder on server...
ssh %SERVER% "cd %REMOTE_PATH% && php seed_static_routes.php"

REM Clear caches
echo Clearing caches...
ssh %SERVER% "cd %REMOTE_PATH% && php artisan config:clear && php artisan view:clear && php artisan cache:clear"

echo.
echo === Done ===
echo Static Route feature deployed!
echo Check CMS: https://cms.smartx.id/admin/static-routes
pause
