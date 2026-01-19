<?php
// Create/update user roles and root user
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Setting up user roles...\n";

// Create root user
$root = User::updateOrCreate(
    ['email' => 'root@smartcms.local'],
    [
        'name' => 'Root',
        'password' => Hash::make('Maja1234'),
        'role' => 'root',
    ]
);
echo "Root user created/updated: {$root->email} (password: Maja1234)\n";

// Update existing demo1 to super_admin
$superAdmin = User::where('email', 'demo1@smartcms.local')->first();
if ($superAdmin) {
    $superAdmin->update(['role' => 'super_admin']);
    echo "demo1 updated to super_admin\n";
} else {
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@smartcms.local',
        'password' => Hash::make('Demo1234'),
        'role' => 'super_admin',
    ]);
    echo "Super Admin created: superadmin@smartcms.local\n";
}

// Create admin user if not exists
$admin = User::firstOrCreate(
    ['email' => 'admin@smartcms.local'],
    [
        'name' => 'Admin',
        'password' => Hash::make('Admin1234'),
        'role' => 'admin',
    ]
);
echo "Admin user ready: {$admin->email}\n";

echo "\n=== USERS ===\n";
$users = User::all(['id', 'name', 'email', 'role']);
foreach ($users as $u) {
    echo "  [{$u->id}] {$u->name} ({$u->email}) - Role: {$u->role}\n";
}
