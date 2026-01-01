@echo off
REM Check Asterisk console output for recent errors 
echo === Check Asterisk console errors ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'core show settings' | grep -i verbose"
echo.
echo === Get Asterisk full log output ===
ssh root@103.154.80.172 "docker logs asterisk --tail 50 2>&1 | grep -iE '(INVITE|6000|6010|error|fail)' | tail -20"
