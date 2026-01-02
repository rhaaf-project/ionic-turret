@echo off
REM Reset dev passwords
scp d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon\reset_dev_passwords.php root@103.154.80.171:/tmp/reset_dev_passwords.php
ssh root@103.154.80.171 "php /tmp/reset_dev_passwords.php"
