@echo off
REM Check current pjsip.conf for NAT settings
echo === Check pjsip.conf (not wizard) ===
ssh root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/pjsip.conf | head -100"
echo.
echo === Check if external_media_address is set ===
ssh root@103.154.80.172 "docker exec asterisk grep -E '(external_media|external_signaling|local_net)' /etc/asterisk/pjsip.conf"
