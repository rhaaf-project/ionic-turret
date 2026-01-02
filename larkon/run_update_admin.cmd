@echo off
REM Update admin@smartx.local to CMS admin
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\update_admin_user.php root@103.154.80.171:/tmp/update_admin_user.php
ssh root@103.154.80.171 "php /tmp/update_admin_user.php"
