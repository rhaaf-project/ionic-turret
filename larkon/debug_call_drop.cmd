@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Recent Asterisk logs (last 100 lines) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk tail -100 /var/log/asterisk/messages 2>/dev/null"

echo.
echo === Check active channels ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels'"

echo.
echo === Check dialplan for from-internal ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan show from-internal' 2>&1 | head -50"

echo DONE
