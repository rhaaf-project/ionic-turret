@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SCP_BIN=C:\Windows\System32\OpenSSH\scp.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR
set USER=root
set HOST=103.154.80.171

echo [1] Upload updated UserResource.php...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\UserResource.php" %USER%@%HOST%:/var/www/html/smartX/app/Filament/Resources/UserResource.php

echo.
echo [2] Fix permissions...
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "chown www-data:www-data /var/www/html/smartX/app/Filament/Resources/UserResource.php"

echo.
echo [3] Clear Laravel cache...
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "cd /var/www/html/smartX && php artisan optimize:clear 2>&1"

echo.
echo DONE - Go to CMS and check Users menu
