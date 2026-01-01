@echo off
REM Check registered endpoints on Asterisk
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1"
