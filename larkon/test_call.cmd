@echo off
REM Test call from Asterisk CLI to 6000
echo === Originate test call to 6000 ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'channel originate PJSIP/6000 application Playback hello-world'"
echo.
echo === Wait and check channels ===
timeout /t 3 >nul
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels'"
