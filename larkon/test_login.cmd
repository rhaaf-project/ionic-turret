@echo off
setlocal ENABLEEXTENSIONS

REM ==== SSH CONFIG ====
set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR
set USER=root
set HOST=103.154.80.171

echo === Test Login API ===
"%SSH_BIN%" %SSH_OPTS% %USER%@%HOST% "curl -s -X POST http://127.0.0.1/api/login -H 'Content-Type: application/json' -H 'Accept: application/json' -d '{\"email\":\"demo1@smartx.local\",\"password\":\"Maja1234\"}'"

echo.
echo === Done ===
