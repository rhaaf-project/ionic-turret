@echo off
REM Upload updated User model
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\User.php root@103.154.80.171:/var/www/html/smartX/app/Models/User.php
ssh root@103.154.80.171 "chown www-data:www-data /var/www/html/smartX/app/Models/User.php && echo 'User model updated'"
