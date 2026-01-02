<?php
// Reset passwords for dev environment
require '/var/www/html/smartX/vendor/autoload.php';
$app = require '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// admin -> 1234
User::where('email', 'admin@smartx.local')->update(['password' => Hash::make('1234')]);
echo "admin@smartx.local -> password: 1234\n";

// demo1-4 -> 123abc
$demos = ['demo1@smartx.local', 'demo2@smartx.local', 'demo3@smartx.local', 'demo4@smartx.local'];
foreach ($demos as $email) {
    User::where('email', $email)->update(['password' => Hash::make('123abc')]);
    echo "$email -> password: 123abc\n";
}

// CMS admin keep admin123
echo "\nCMS Admin (cmsadmin@smartx.local) -> keeps Admin@123\n";
echo "\nDone!\n";
