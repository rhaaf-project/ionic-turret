@echo off
REM Check active channels and clear ghost sessions
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show channels' 2>&1"
