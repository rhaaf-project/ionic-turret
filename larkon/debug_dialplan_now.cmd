@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Full dialplan for internal context ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan show internal'"

echo.
echo === Check if 6010 is registered ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts' 2>&1 | grep 6010"

echo.
echo === Current channels (run while call active) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels verbose'"

echo DONE
