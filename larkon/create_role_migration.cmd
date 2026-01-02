@echo off
REM Step 1: Create migration for role column
ssh root@103.154.80.171 "cd /var/www/html/smartX && php artisan make:migration add_role_to_users_table --table=users"
