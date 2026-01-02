#!/bin/bash
cd /var/www/html/smartX

# Use Laravel's Hash facade properly 
php artisan tinker --execute="
use Illuminate\Support\Facades\Hash;
\$hash = Hash::make('admin123');
\App\Models\User::where('email', 'admin@smartx.local')->update(['password' => \$hash]);
echo 'Admin password reset to admin123';
"
