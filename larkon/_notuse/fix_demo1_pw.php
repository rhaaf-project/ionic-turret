<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'demo1@smartx.local';
$newPassword = 'Maja1234';

$user = User::where('email', $email)->first();

if ($user) {
    $user->password = Hash::make($newPassword);
    $user->save();
    echo "SUCCESS: Password updated for $email\n";
    echo "New hash starts with: " . substr($user->password, 0, 10) . "\n";
} else {
    echo "ERROR: User $email not found\n";
}
