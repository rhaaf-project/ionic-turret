@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Verify both endpoints registered ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts'"

echo.
echo === Add media_encryption_optimistic to pjsip.conf for WebRTC endpoints ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk sed -i 's/webrtc=yes/webrtc=yes\nmedia_encryption_optimistic=yes/g' /etc/asterisk/pjsip.conf"

echo.
echo === Reload pjsip ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Verify 6000 now has media_encryption_optimistic ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' 2>&1 | grep 'media_encryption'"

echo DONE - Now test call from browser to Phoner!
