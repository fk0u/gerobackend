<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== FIXING USERS TABLE STRUCTURE ===\n\n";

// Backup existing user data
echo "📦 Backing up existing users...\n";
$existingUsers = DB::table('users')->get()->toArray();
echo "✅ Backed up " . count($existingUsers) . " users\n\n";

// Drop the corrupt table
echo "🗑️  Dropping corrupt users table...\n";
Schema::dropIfExists('users');
echo "✅ Table dropped\n\n";

// Recreate with correct structure
echo "🔨 Creating users table with correct structure...\n";
Schema::create('users', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
    
    // Extended fields
    $table->enum('role', ['end_user', 'mitra', 'admin'])->default('end_user');
    $table->string('profile_picture')->nullable();
    $table->string('phone', 20)->nullable();
    $table->text('address')->nullable();
    $table->enum('subscription_status', ['active', 'inactive', 'expired'])->default('inactive');
    $table->integer('points')->default(0);
    
    // Mitra-specific fields
    $table->string('employee_id')->nullable()->unique();
    $table->string('vehicle_type')->nullable();
    $table->string('vehicle_plate')->nullable();
    $table->string('work_area')->nullable();
    $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
    $table->decimal('rating', 3, 2)->nullable();
    $table->integer('total_collections')->default(0);
});
echo "✅ Table created\n\n";

// Restore user data
echo "📥 Restoring user data...\n";
foreach ($existingUsers as $user) {
    DB::table('users')->insert([
        'name' => $user->name,
        'email' => $user->email,
        'email_verified_at' => $user->email_verified_at,
        'password' => $user->password,
        'remember_token' => $user->remember_token,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'role' => $user->role ?: 'end_user',
        'profile_picture' => $user->profile_picture,
        'phone' => $user->phone,
        'address' => $user->address,
        'subscription_status' => $user->subscription_status ?: 'inactive', // Handle NULL
        'points' => $user->points ?? 0,
        'employee_id' => $user->employee_id,
        'vehicle_type' => $user->vehicle_type,
        'vehicle_plate' => $user->vehicle_plate,
        'work_area' => $user->work_area,
        'status' => $user->status ?: 'active', // Handle NULL
        'rating' => $user->rating,
        'total_collections' => $user->total_collections ?? 0,
    ]);
    echo "  ✅ Restored: {$user->email}\n";
}

echo "\n🎉 Users table recreated successfully!\n";
echo "   Total users restored: " . count($existingUsers) . "\n\n";

// Verify
echo "=== VERIFYING NEW STRUCTURE ===\n\n";
$columns = DB::select("SHOW COLUMNS FROM users LIMIT 5");
foreach ($columns as $column) {
    echo sprintf("%-20s %-25s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Key
    );
}
echo "...\n\n";

echo "✅ ALL DONE!\n";
