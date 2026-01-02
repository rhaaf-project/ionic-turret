<?php
// Create CMS Admin user
require '/var/www/html/smartX/vendor/autoload.php';
$app = require '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if exists
$existing = User::where('email', 'cmsadmin@smartx.local')->first();
if ($existing) {
    echo "CMS Admin already exists with ID: " . $existing->id . "\n";
    exit(0);
}

// Create new CMS admin
$user = User::create([
    'name' => 'CMS Admin',
    'email' => 'cmsadmin@smartx.local',
    'password' => Hash::make('Admin@123'),
    'role' => 'admin',
    'use_ext' => null,
]);

echo "CMS Admin created with ID: " . $user->id . "\n";
echo "Email: cmsadmin@smartx.local\n";
echo "Password: Admin@123\n";
