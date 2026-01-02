@echo off
REM Upload and run CMS admin creation script
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\create_cms_admin.php root@103.154.80.171:/tmp/create_cms_admin.php
ssh root@103.154.80.171 "php /tmp/create_cms_admin.php"
