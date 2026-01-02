@echo off
REM Upload migration file and run migration
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\add_role_migration.php root@103.154.80.171:/var/www/html/smartX/database/migrations/2026_01_02_133239_add_role_to_users_table.php
ssh root@103.154.80.171 "cd /var/www/html/smartX && chown www-data:www-data database/migrations/2026_01_02_133239_add_role_to_users_table.php && php artisan migrate"
