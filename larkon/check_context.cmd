@echo off
REM Check full extensions.conf and context
echo === Check context in pjsip_wizard.conf ===
ssh root@103.154.80.172 "docker exec asterisk grep 'context' /etc/asterisk/pjsip_wizard.conf | head -5"
echo.
echo === List contexts in extensions.conf ===
ssh root@103.154.80.172 "docker exec asterisk grep '^\[' /etc/asterisk/extensions.conf"
