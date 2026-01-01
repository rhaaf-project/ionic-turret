@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Check current pjsip_wizard.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/pjsip_wizard.conf 2>/dev/null | head -80"

echo.
echo === Check general pjsip.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/pjsip.conf 2>/dev/null | head -50"

echo DONE
