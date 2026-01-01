@echo off
REM Check contacts for 6000, 6001, 6002
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts' 2>&1" | findstr "6000 6001 6002"
