#!/bin/bash
cd /var/www/html/smartX
php artisan tinker --execute="print_r(\App\Models\User::all()->toArray());"
