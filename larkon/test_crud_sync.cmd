@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo ========================================
echo TEST: Extension CRUD Sync to Asterisk
echo ========================================

echo.
echo [1] CREATE: Add test extension 9999...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php -r \"require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); \$ext = App\Models\Extension::create(['extension' => '9999', 'name' => 'Test CRUD', 'secret' => 'test123', 'context' => 'from-internal', 'is_active' => true]); echo 'Created: ' . \$ext->extension;\" 2>&1"

echo.
echo [2] CHECK: Extension 9999 in Asterisk...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 9999' 2>&1 | head -10"

echo.
echo [3] EDIT: Update extension 9999 secret...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php -r \"require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); \$ext = App\Models\Extension::where('extension', '9999')->first(); \$ext->secret = 'newpass456'; \$ext->save(); echo 'Updated secret for: ' . \$ext->extension;\" 2>&1"

echo.
echo [4] CHECK: Endpoint still exists after edit...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1 | grep 9999"

echo.
echo [5] DELETE: Remove extension 9999...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && php -r \"require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); App\Models\Extension::where('extension', '9999')->delete(); echo 'Deleted extension 9999';\" 2>&1"

echo.
echo [6] CHECK: Extension 9999 should be GONE from Asterisk...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1 | grep 9999 || echo 'NOT FOUND (expected)'"

echo.
echo ========================================
echo TEST COMPLETE
echo ========================================
