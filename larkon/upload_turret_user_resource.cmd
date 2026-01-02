@echo off
REM Upload updated TurretUserResource
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\TurretUserResource.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/TurretUserResource.php
ssh root@103.154.80.171 "chown www-data:www-data /var/www/html/smartX/app/Filament/Resources/TurretUserResource.php && cd /var/www/html/smartX && php artisan optimize:clear"
echo Done!
