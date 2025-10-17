<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== TESTING MITRA AND ADMIN LOGIN ===\n\n";

// Test mitra user
echo "Testing Mitra User:\n";
$mitra = User::where('email', 'mitra@test.com')->first();
if ($mitra) {
    echo "✅ User found:\n";
    echo "   ID: {$mitra->id}\n";
    echo "   Name: {$mitra->name}\n";
    echo "   Email: {$mitra->email}\n";
    echo "   Role: {$mitra->role}\n";
    echo "   Work Area: " . ($mitra->work_area ?? 'NULL') . "\n";
    
    // Test password
    $passwordCheck = Hash::check('password123', $mitra->password);
    echo "   Password Check: " . ($passwordCheck ? "✅ MATCH" : "❌ NO MATCH") . "\n";
} else {
    echo "❌ Mitra user not found!\n";
}

echo "\n";

// Test admin user
echo "Testing Admin User:\n";
$admin = User::where('email', 'admin@test.com')->first();
if ($admin) {
    echo "✅ User found:\n";
    echo "   ID: {$admin->id}\n";
    echo "   Name: {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Role: {$admin->role}\n";
    echo "   Work Area: " . ($admin->work_area ?? 'NULL') . "\n";
    
    // Test password
    $passwordCheck = Hash::check('password123', $admin->password);
    echo "   Password Check: " . ($passwordCheck ? "✅ MATCH" : "❌ NO MATCH") . "\n";
} else {
    echo "❌ Admin user not found!\n";
}

echo "\n=== CHECKING SUBSCRIPTION_PLANS TABLE ===\n";
try {
    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM subscription_plans");
    echo "Columns in subscription_plans table:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
