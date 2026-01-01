@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Enable RTP debug ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'rtp set debug on'"

echo.
echo === Enable PJSIP logger ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip set logger on'"

echo.
echo === Set verbose 10 ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'core set verbose 10'"

echo.
echo === Now make a test call and check logs ===
echo Press any key after making the call...
pause

echo.
echo === Get recent logs (last 100 lines) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker logs asterisk 2>&1 | tail -100"

echo DONE
