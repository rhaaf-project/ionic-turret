<?php
// Update admin@smartx.local to be CMS admin with password admin123
require '/var/www/html/smartX/vendor/autoload.php';
$app = require '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@smartx.local')->first();

if (!$user) {
    echo "User not found!\n";
    exit(1);
}

// Update role to admin and reset password
$user->update([
    'role' => 'admin',
    'password' => Hash::make('admin123'),
]);

echo "Updated admin@smartx.local:\n";
echo "- Role: admin (can access CMS)\n";
echo "- Password: admin123\n";
echo "- Extension: " . ($user->use_ext ?: 'none') . " (can still use turret if has extension)\n";
