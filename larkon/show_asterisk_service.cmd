@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Full AsteriskService.php ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cat /var/www/html/smartX/app/Services/AsteriskService.php"

echo.
echo DONE
