@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Check extensions.conf dialplan ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/extensions.conf"

echo.
echo === Check if 6011 endpoint exists ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' 2>&1 | head -10"

echo.
echo === Check pjsip logger for recent call ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker logs asterisk 2>&1 | tail -30"

echo DONE
