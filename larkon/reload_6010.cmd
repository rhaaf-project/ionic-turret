@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Force unregister 6010 ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts' 2>&1 | grep 6010"

echo.
echo === Full pjsip reload ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Wait 3 seconds for Phoner to re-register ===
timeout /t 3 /nobreak

echo.
echo === Check 6010 endpoint config after reload ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' 2>&1 | grep -iE '(webrtc|media_encryption|ice_support)'"

echo.
echo === Check if 6010 re-registered ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts' 2>&1 | grep 6010"

echo DONE - Now re-open Phoner and test call
