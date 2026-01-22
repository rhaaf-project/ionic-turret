@echo off
REM Upload topology PNG icons and blade file to server
REM Usage: upload_topology_icons.cmd

set SERVER=root@172.16.10.129
set REMOTE_PATH=/var/www/html
set LOCAL_PATH=%~dp0

echo === Uploading Topology Icons ===

REM Create remote directory for topology icons
echo Creating remote directory...
ssh %SERVER% "mkdir -p %REMOTE_PATH%/public/images/topology"

REM Upload PNG files
echo Uploading PNG icons...
scp "%LOCAL_PATH%..\head-office.png" %SERVER%:%REMOTE_PATH%/public/images/topology/
scp "%LOCAL_PATH%..\branch.png" %SERVER%:%REMOTE_PATH%/public/images/topology/
scp "%LOCAL_PATH%..\branch-sbc.png" %SERVER%:%REMOTE_PATH%/public/images/topology/

REM Upload blade template
echo Uploading topology-map.blade.php...
scp "%LOCAL_PATH%smartX_cms\views\filament\pages\topology-map.blade.php" %SERVER%:%REMOTE_PATH%/resources/views/filament/pages/

REM Set permissions
echo Setting permissions...
ssh %SERVER% "chmod 644 %REMOTE_PATH%/public/images/topology/*.png"
ssh %SERVER% "chmod 644 %REMOTE_PATH%/resources/views/filament/pages/topology-map.blade.php"

REM Clear view cache
echo Clearing view cache...
ssh %SERVER% "cd %REMOTE_PATH% && php artisan view:clear"

echo === Done ===
echo Icons uploaded to: %REMOTE_PATH%/public/images/topology/
echo Blade file uploaded to: %REMOTE_PATH%/resources/views/filament/pages/topology-map.blade.php
pause
