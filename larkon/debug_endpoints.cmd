@echo off
REM Check raw endpoint status - save to file
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints'" > endpoints_raw.txt
echo Saved to endpoints_raw.txt
type endpoints_raw.txt | findstr "Endpoint"
