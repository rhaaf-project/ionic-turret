@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM ===============================
REM ENV
REM ===============================
set CI=true
set IONIC_TELEMETRY=0

REM ===============================
REM SSH CONFIG
REM ===============================
set SSH_USER=root
set SSH_HOST=103.154.80.171
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL

REM ===============================
REM [1] BUILD IONIC
REM ===============================
echo [1/4] Building Ionic Project...
powershell -Command "& {echo 'y' | npm run build -- --confirm 2>&1 | Select-Object -Last 5}"

REM ===============================
REM [2] BUILD ELECTRON
REM ===============================
echo [2/4] Building Electron Assets...
echo y | call npm run electron:build -- --confirm

REM ===============================
REM [3] CLEAN REMOTE ADMIN (SSH AUTO)
REM ===============================
echo [3/4] Cleaning remote admin directory...
ssh %SSH_OPTS% %SSH_USER%@%SSH_HOST% ^
"rm -rf /var/www/html/admin/*"

REM ===============================
REM [4] UPLOAD BUILD (SCP AUTO)
REM ===============================
echo [4/4] Uploading files to server...
scp -r %SSH_OPTS% ^
dist/larkon/browser/* %SSH_USER%@%SSH_HOST%:/var/www/html/admin/

echo.
echo =====================================
echo DEPLOY FINISHED (NO SSH PROMPT)
echo =====================================
pause
