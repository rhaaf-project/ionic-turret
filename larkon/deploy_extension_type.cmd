@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH/SCP CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SCP_BIN=C:\Windows\System32\OpenSSH\scp.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo =============================================
echo Deploy Extension Type Feature to CMS (171)
echo =============================================

echo.
echo [1] Upload migration file...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\add_extension_type_migration.php" root@103.154.80.171:/var/www/html/smartX/database/migrations/2026_01_08_100000_add_extension_type.php

echo.
echo [2] Upload Extension model...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\Extension.php" root@103.154.80.171:/var/www/html/smartX/app/Models/Extension.php

echo.
echo [3] Upload AsteriskService...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\AsteriskService.php" root@103.154.80.171:/var/www/html/smartX/app/Services/AsteriskService.php

echo.
echo [4] Upload ExtensionResource...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\ExtensionResource.php" root@103.154.80.171:/var/www/html/smartX/app/Filament/Resources/ExtensionResource.php

echo.
echo [5] Run migration...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php artisan migrate --force"

echo.
echo [6] Clear cache...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php artisan cache:clear && php artisan config:clear && php artisan view:clear"

echo.
echo [7] Update existing extensions to correct type...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c \"UPDATE extensions SET type = 'webrtc' WHERE extension::int BETWEEN 6000 AND 6009; UPDATE extensions SET type = 'softphone' WHERE extension::int >= 6010;\""

echo.
echo =============================================
echo Done! Extension Type feature deployed.
echo =============================================
