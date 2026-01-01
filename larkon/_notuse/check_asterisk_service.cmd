@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR
set USER=root
set HOST=103.154.80.171

echo === Full AsteriskService.php ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "cat /var/www/html/smartX/app/Services/AsteriskService.php"

echo.
echo === Test SSH 171 to 172 ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "ssh -o StrictHostKeyChecking=no -o BatchMode=yes root@103.154.80.172 'echo OK-172-reachable'"

echo.
echo DONE
