@echo off
REM Check dialplan and 6011 config
echo === PJSIP 6011 Config ===
ssh root@103.154.80.172 "docker exec asterisk grep -A15 '\[6011\]' /etc/asterisk/pjsip_wizard.conf"
echo.
echo === Dialplan extensions.conf ===
ssh root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/extensions.conf | head -50"
