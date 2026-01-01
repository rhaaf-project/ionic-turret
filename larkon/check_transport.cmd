@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Check pjsip.conf transports ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/pjsip.conf 2>/dev/null | head -80"

echo.
echo === Check rtp.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/rtp.conf 2>/dev/null"

echo.
echo === Check 6000 and 6010 registered contacts ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts'"

echo.
echo === Compare endpoint 6000 vs 6010 key settings ===
echo --- 6000 (WebRTC) ---
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' 2>&1 | grep -iE '(webrtc|dtls|ice_support|media_encryption|transport|context)'"

echo.
echo --- 6010 (Softphone) ---
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' 2>&1 | grep -iE '(webrtc|dtls|ice_support|media_encryption|transport|context)'"

echo DONE
