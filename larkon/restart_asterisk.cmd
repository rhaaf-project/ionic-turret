@echo off
REM Restart Asterisk to force re-read config
echo === Restarting Asterisk container ===
ssh root@103.154.80.172 "docker restart asterisk"
echo.
echo === Wait for startup ===
timeout /t 10 >nul
echo === Verify NAT settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(rtp_symmetric|rewrite_contact|force_rport)'"
