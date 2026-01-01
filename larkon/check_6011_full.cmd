@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Current pjsip.conf (checking 6011 section) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/pjsip.conf | tail -50"

echo.
echo === Check ALL 6011 related config ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' 2>&1 | grep -iE '(webrtc|media_encrypt|dtls|ice)'"

echo DONE
