@echo off
REM Fix context from 'from-internal' to 'internal' and reload
echo === Fixing context from-internal to internal ===
ssh root@103.154.80.172 "docker exec asterisk sed -i 's/from-internal/internal/g' /etc/asterisk/pjsip_wizard.conf"
echo.
echo === Reloading PJSIP ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip reload'"
echo.
echo === Verify context ===
ssh root@103.154.80.172 "docker exec asterisk grep 'endpoint/context' /etc/asterisk/pjsip_wizard.conf | head -3"
echo.
echo === Setup Ghost Channel Cleanup Cron (every 5 min) ===
ssh root@103.154.80.172 "(crontab -l 2>/dev/null | grep -v 'channel request hangup'; echo '*/5 * * * * docker exec asterisk asterisk -rx \"channel request hangup all\" >/dev/null 2>&1') | crontab -"
ssh root@103.154.80.172 "crontab -l | grep hangup"
echo Done!
