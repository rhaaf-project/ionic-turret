@echo off
setlocal ENABLEEXTENSIONS

REM ==== FORCE SSH BINARY ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe

REM ==== FORCE NON-INTERACTIVE ====
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

set USER=root

echo [INFO] SSH binary:
"%SSH_BIN%" -V

echo [INFO] Test 171
"%SSH_BIN%" %SSH_OPTS% %USER%@171 "echo OK-171" || goto :fail

echo [INFO] Test 172
"%SSH_BIN%" %SSH_OPTS% %USER%@172 "echo OK-172" || goto :fail

echo [SUCCESS] NO ACCEPT / NO PASSWORD
exit /b 0

:fail
echo [FATAL] SSH STILL ASKING OR FAILED
exit /b 1
