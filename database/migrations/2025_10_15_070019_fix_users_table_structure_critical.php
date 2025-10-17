<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * WARNING: This will DROP and RECREATE users table!
     * All user data will be LOST unless backed up!
     */
    public function up(): void
    {
        // Backup existing user data
        $existingUsers = DB::table('users')->get()->toArray();
        
        // Drop the corrupt table
        Schema::dropIfExists('users');
        
        // Recreate with correct structure
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Extended fields from migration 2025_09_24_000003_add_fields_to_users_table
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
        
        // Restore user data
        foreach ($existingUsers as $user) {
            DB::table('users')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture,
                'phone' => $user->phone,
                'address' => $user->address,
                'subscription_status' => $user->subscription_status,
                'points' => $user->points ?? 0,
                'employee_id' => $user->employee_id,
                'vehicle_type' => $user->vehicle_type,
                'vehicle_plate' => $user->vehicle_plate,
                'work_area' => $user->work_area,
                'status' => $user->status,
                'rating' => $user->rating,
                'total_collections' => $user->total_collections ?? 0,
            ]);
        }
        
        echo "\n✅ Users table recreated successfully!\n";
        echo "   Restored " . count($existingUsers) . " users\n\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration safely
        echo "WARNING: Cannot reverse this migration - would lose all user data!\n";
    }
};
