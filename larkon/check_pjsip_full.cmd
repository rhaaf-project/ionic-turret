@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Full pjsip endpoint 6000 ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000'"

echo.
echo === Full pjsip endpoint 6010 ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010'"

echo.
echo === Check DTLS/SRTP settings ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' 2>&1 | grep -iE '(dtls|srtp|media_encryption|ice)'"

echo.
echo === Recent calls in CDR ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk tail -20 /var/log/asterisk/cdr-csv/Master.csv 2>/dev/null"

echo DONE
