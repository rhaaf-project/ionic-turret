@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo [1] Check recent Laravel logs...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "tail -50 /var/www/html/smartX/storage/logs/laravel.log 2>/dev/null | grep -E '(Extension|Asterisk|Error|error)'"

echo.
echo [2] Check if 9999 in PostgreSQL...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c \"SELECT * FROM extensions WHERE extension = '9999';\""

echo.
echo [3] Check SSH from 171 to 172...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "ssh -o StrictHostKeyChecking=no root@103.154.80.172 'echo SSH-OK'"

echo.
echo DONE
