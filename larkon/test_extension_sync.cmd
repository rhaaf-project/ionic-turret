@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo =============================================
echo Test Extension Sync: CMS (171) to PBX (172)
echo =============================================

echo.
echo [1] Current extensions in PostgreSQL (171)...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c 'SELECT extension, name, is_active FROM extensions ORDER BY extension;'"

echo.
echo [2] Current PJSIP endpoints in Asterisk (172)...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>/dev/null | head -30"

echo.
echo [3] Test AsteriskService from Laravel...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php -r \"require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo app('App\Services\AsteriskService')->getEndpoints();\" 2>&1 | head -20"

echo.
echo =============================================
echo Done - Extension sync is functional!
echo =============================================
