@echo off
REM Check Asterisk verbose logs for call issues
echo === Check last 50 lines of Asterisk log ===
ssh root@103.154.80.172 "docker exec asterisk tail -50 /var/log/asterisk/messages"
echo.
echo === Check active channels and calls ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels'"
