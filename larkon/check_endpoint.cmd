@echo off
REM Check actual endpoint context for 6011
echo === PJSIP Show Endpoint 6011 ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' | grep -i context"
echo.
echo === Check pjsip_wizard.conf for 6011 ===
ssh root@103.154.80.172 "docker exec asterisk grep -A12 '\[6011\]' /etc/asterisk/pjsip_wizard.conf | grep context"
