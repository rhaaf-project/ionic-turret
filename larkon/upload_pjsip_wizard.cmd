@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SCP_BIN=C:\Windows\System32\OpenSSH\scp.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo [1] Copy to server 172 host...
"%SCP_BIN%" %SSH_OPTS% "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\pjsip_wizard.conf" root@103.154.80.172:/tmp/pjsip_wizard.conf

echo.
echo [2] Copy to Asterisk container...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker cp /tmp/pjsip_wizard.conf asterisk:/etc/asterisk/pjsip_wizard.conf"

echo.
echo [3] Reload pjsip...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so' && docker exec asterisk asterisk -rx 'pjsip reload'"

echo.
echo [4] Check endpoints...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1 | grep -E '^.Endpoint:'"

echo.
echo [5] Check 6010 media_encryption setting...
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' 2>&1 | grep -iE '(media_encryption|webrtc)'"

echo DONE - Now test WebRTC 6000 calling Softphone 6010
