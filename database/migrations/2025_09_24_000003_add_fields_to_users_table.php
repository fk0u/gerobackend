<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('end_user');
            $table->string('profile_picture')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('subscription_status')->nullable();
            $table->integer('points')->default(0);

            // Mitra-specific optional fields
            $table->string('employee_id')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('work_area')->nullable();
            $table->string('status')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('total_collections')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role','profile_picture','phone','address','subscription_status','points',
                'employee_id','vehicle_type','vehicle_plate','work_area','status','rating','total_collections'
            ]);
        });
    }
};
