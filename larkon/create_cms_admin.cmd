@echo off
REM Create CMS admin user using Laravel Hash
REM Password will be 'Admin@123' 
ssh root@103.154.80.171 "cd /var/www/html/smartX && php artisan tinker --execute=\"App\Models\User::create(['name'=>'CMS Admin','email'=>'cmsadmin@smartx.local','password'=>bcrypt('Admin@123'),'role'=>'admin']);\""
