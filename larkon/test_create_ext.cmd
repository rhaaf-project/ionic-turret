@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Create extension via artisan tinker ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "cd /var/www/html/smartX && echo \"App\Models\Extension::create(['extension' => '9999', 'name' => 'Test CRUD', 'secret' => 'test123', 'context' => 'from-internal', 'is_active' => true]);\" | php artisan tinker 2>&1"

echo.
echo === Check in PostgreSQL ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c \"SELECT extension, name, is_active FROM extensions WHERE extension >= '9000' ORDER BY extension;\""

echo.
echo === Check in Asterisk ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1 | grep -E '(9999|Endpoint:.*9)'"

echo DONE
