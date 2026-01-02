@echo off
REM Check Laravel project structure
ssh root@103.154.80.171 "ls -la /var/www/html/smartX/app/Models/ && ls -la /var/www/html/smartX/app/Filament/ 2>/dev/null"
