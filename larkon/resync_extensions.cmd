@echo off
REM Clear and regenerate pjsip_wizard.conf with NAT settings
echo === Clear existing pjsip_wizard.conf ===
ssh root@103.154.80.172 "docker exec asterisk bash -c 'echo \"\" > /etc/asterisk/pjsip_wizard.conf'"
echo.
echo === Upload new sync script and AsteriskService ===
scp "D:/02____WORKS/04___Server/Projects/ionic/trader-turret/larkon/AsteriskService.php" root@103.154.80.171:/var/www/html/smartX/app/Services/
scp "D:/02____WORKS/04___Server/Projects/ionic/trader-turret/larkon/sync_extensions.php" root@103.154.80.171:/var/www/html/smartX/
echo.
echo === Clear cache and run sync ===
ssh root@103.154.80.171 "cd /var/www/html/smartX && php artisan cache:clear && sudo -u www-data php sync_extensions.php"
