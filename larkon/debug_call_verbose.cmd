@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Enable debug and verbose ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'core set verbose 10' && docker exec asterisk asterisk -rx 'pjsip set logger on'"

echo.
echo === Recent full log (last 200 lines) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk tail -200 /var/log/asterisk/full 2>/dev/null"

echo.
echo === Check for any error in logs ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk tail -500 /var/log/asterisk/messages 2>/dev/null | grep -iE '(error|warning|hangup|bye|6010|6000)'"

echo DONE
