@echo off
REM Upload all Turret User Resource files

REM Create directory for TurretUserResource pages
ssh root@103.154.80.171 "mkdir -p /var/www/html/smartX/app/Filament/Resources/TurretUserResource/Pages"

REM Upload TurretUserResource.php
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\TurretUserResource.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/TurretUserResource.php

REM Upload TurretUserResource pages
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\ListTurretUsers.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/TurretUserResource/Pages/ListTurretUsers.php
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\CreateTurretUser.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/TurretUserResource/Pages/CreateTurretUser.php
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\EditTurretUser.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/TurretUserResource/Pages/EditTurretUser.php

REM Upload updated UserResource
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\UserResource_updated.php root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/UserResource.php

REM Fix permissions
ssh root@103.154.80.171 "chown -R www-data:www-data /var/www/html/smartX/app/Filament/Resources/"

REM Clear cache
ssh root@103.154.80.171 "cd /var/www/html/smartX && php artisan optimize:clear"

echo Done!
