@echo off
REM Check Filament resources
ssh root@103.154.80.171 "ls -la /var/www/html/smartX/app/Filament/Resources/ && cat /var/www/html/smartX/app/Filament/Resources/UserResource.php"
