<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'root@smartcms.local')->first();
if ($user) {
    $user->password = Hash::make('Maja1234');
    $user->save();
    echo "Password reset for root@smartcms.local to Maja1234\n";
} else {
    echo "User not found, creating...\n";
    $user = User::create([
        'name' => 'Root',
        'email' => 'root@smartcms.local',
        'password' => Hash::make('Maja1234'),
        'role' => 'root',
    ]);
    echo "Created root user\n";
}
echo "Done!\n";
