<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING TEST USERS ===\n\n";

// Create Mitra User
$mitra = User::firstOrCreate(
    ['email' => 'mitra@test.com'],
    [
        'name' => 'Test Mitra',
        'password' => Hash::make('password123'),
        'role' => 'mitra',
        'phone' => '08123456789',
        'vehicle_type' => 'truck',
        'vehicle_plate' => 'B1234XYZ',
        'work_area' => 'Jakarta',
        'status' => 'active',
        'rating' => 4.5,
    ]
);

if ($mitra->wasRecentlyCreated) {
    echo "✅ Created Mitra User\n";
} else {
    echo "ℹ️  Mitra User already exists\n";
}
echo "   Email: mitra@test.com\n";
echo "   Password: password123\n";
echo "   Role: mitra\n\n";

// Create Admin User
$admin = User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Test Admin',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'phone' => '08198765432',
    ]
);

if ($admin->wasRecentlyCreated) {
    echo "✅ Created Admin User\n";
} else {
    echo "ℹ️  Admin User already exists\n";
}
echo "   Email: admin@test.com\n";
echo "   Password: password123\n";
echo "   Role: admin\n\n";

// Verify existing end_user
$end_user = User::where('email', 'daffa@gmail.com')->first();
if ($end_user) {
    echo "✅ End User exists\n";
    echo "   Email: daffa@gmail.com\n";
    echo "   Password: password123\n";
    echo "   Role: {$end_user->role}\n\n";
}

echo "=== SUMMARY ===\n";
echo "Total users created: " . User::count() . "\n";
echo "\nTest users ready for role-based testing:\n";
echo "  - end_user: daffa@gmail.com\n";
echo "  - mitra: mitra@test.com\n";
echo "  - admin: admin@test.com\n";
echo "  - All passwords: password123\n\n";

echo "=== GENERATING TOKENS FOR TESTING ===\n\n";

// Generate tokens for each user
$users = [
    'end_user' => $end_user,
    'mitra' => $mitra,
    'admin' => $admin,
];

foreach ($users as $role => $user) {
    if ($user) {
        // Delete old tokens
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('test-token');
        echo "{$role} token: {$token->plainTextToken}\n";
    }
}

echo "\n✅ All test users created and tokens generated!\n";
