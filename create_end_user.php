<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING END USER ===\n\n";

$endUser = User::firstOrCreate(
    ['email' => 'daffa@gmail.com'],
    [
        'name' => 'User Daffa',
        'password' => Hash::make('password123'),
        'role' => 'end_user',
        'profile_picture' => 'assets/img_friend1.png',
        'phone' => '081234567890',
        'address' => 'Jl. Merdeka No. 1, Jakarta',
        'subscription_status' => 'active',
        'points' => 50,
        'status' => 'active'
    ]
);

echo "✅ Created End User\n";
echo "   Email: daffa@gmail.com\n";
echo "   Password: password123\n";
echo "   Role: end_user\n\n";

// Generate token
$token = $endUser->createToken('test-end-user');
echo "Token: {$token->plainTextToken}\n\n";

echo "✅ All done!\n";
