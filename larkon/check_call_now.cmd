@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Check active channels NOW (run during call) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels verbose'"

echo.
echo === Check recent call logs ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker logs asterisk 2>&1 | tail -50"

echo.
echo === Check if 6000 and 6010 both registered ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts'"

echo DONE
