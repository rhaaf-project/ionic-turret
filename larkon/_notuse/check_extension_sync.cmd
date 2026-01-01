@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR
set USER=root
set HOST=103.154.80.171

echo === Check ExtensionResource.php ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "cat /var/www/html/smartX/app/Filament/Resources/ExtensionResource.php"

echo.
echo === Check AsteriskService if exists ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "cat /var/www/html/smartX/app/Services/AsteriskService.php 2>/dev/null | head -100"

echo.
echo === Check Extension model ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "cat /var/www/html/smartX/app/Models/Extension.php"

echo.
echo DONE
