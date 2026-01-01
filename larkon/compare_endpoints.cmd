@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Test call 6000 to 6001 (both WebRTC) - should work longer ===
echo This is to compare - 6001 is WebRTC, 6010 is softphone

echo.
echo === Check if 6001 is registered ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show aor 6001'"

echo.
echo === Compare 6000 and 6010 codec negotiation ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' 2>&1 | grep allow"
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' 2>&1 | grep allow"

echo.
echo === Check CLI log for recent call ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker logs asterisk 2>&1 | tail -100 | grep -iE '(6010|6000|hangup|bye|channel|dial)'"

echo DONE
