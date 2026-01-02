@echo off
REM Check User model and Filament config
ssh root@103.154.80.171 "cat /var/www/html/smartX/app/Models/User.php && echo '---SEPARATOR---' && cat /var/www/html/smartX/app/Providers/Filament/AdminPanelProvider.php 2>/dev/null"
