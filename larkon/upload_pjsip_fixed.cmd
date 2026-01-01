@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SCP_BIN=C:\Windows\System32\OpenSSH\scp.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Backup current pjsip.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk cp /etc/asterisk/pjsip.conf /etc/asterisk/pjsip.conf.backup"

echo.
echo === Upload new pjsip_fixed.conf ===
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\pjsip_fixed.conf" root@103.154.80.172:/tmp/pjsip.conf

echo.
echo === Copy to Asterisk container ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker cp /tmp/pjsip.conf asterisk:/etc/asterisk/pjsip.conf"

echo.
echo === Reload pjsip ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Verify endpoints ===
echo --- 6000 (WebRTC) ---
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' 2>&1 | grep -E '(webrtc|media_encryption)'"
echo --- 6010 (Softphone) ---
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' 2>&1 | grep -E '(webrtc|media_encryption)'"
echo --- 6011 (Softphone) ---
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' 2>&1 | grep -E '(webrtc|media_encryption)'"

echo DONE - Re-register browser and softphones, then test!
