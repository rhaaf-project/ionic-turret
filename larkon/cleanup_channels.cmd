@echo off
REM Cleanup all ghost channels
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'channel request hangup all' 2>&1"
echo Channels cleared. Checking status...
timeout /t 2 >nul
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels count' 2>&1"
