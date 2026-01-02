@echo off
ssh root@103.154.80.171 "cd /var/www/html/smartX && php artisan tinker --execute=\"print_r(App\\Models\\User::select('id','name','email','role','use_ext')->get()->toArray());\""
