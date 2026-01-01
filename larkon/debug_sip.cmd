@echo off
REM Enable SIP debug and watch for incoming calls
echo === Enable PJSIP debug ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip set logger on'"
echo.
echo === Check PJSIP status ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' | head -30"
echo.
echo === Check contacts for 6000 ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts' | grep -E '(6000|6010)'"
