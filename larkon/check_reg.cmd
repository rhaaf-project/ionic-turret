@echo off
REM Check registration status of all contacts
echo === All registered contacts ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts'"
echo.
echo === Check 6010 endpoint state ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(State|Contact)'"
echo.
echo === Check 6007 endpoint state ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6007' | grep -E '(State|Contact)'"
