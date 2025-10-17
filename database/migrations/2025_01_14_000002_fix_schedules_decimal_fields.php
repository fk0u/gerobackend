<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing data: Convert invalid values to NULL
        // This prevents "Unable to cast value to decimal" errors
        
        // First, make columns nullable if they aren't already
        Schema::table('schedules', function (Blueprint $table) {
            $table->decimal('pickup_latitude', 10, 8)->nullable()->change();
            $table->decimal('pickup_longitude', 11, 8)->nullable()->change();
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->decimal('price', 10, 2)->nullable()->change();
        });
        
        // Then clean up invalid data
        // Use CASE statement to safely handle conversion
        DB::statement("
            UPDATE schedules 
            SET pickup_latitude = NULL 
            WHERE pickup_latitude = 0 
               OR pickup_latitude IS NULL
        ");
        
        DB::statement("
            UPDATE schedules 
            SET pickup_longitude = NULL 
            WHERE pickup_longitude = 0 
               OR pickup_longitude IS NULL
        ");
        
        DB::statement("
            UPDATE schedules 
            SET latitude = NULL 
            WHERE latitude = 0
        ");
        
        DB::statement("
            UPDATE schedules 
            SET longitude = NULL 
            WHERE longitude = 0
        ");
        
        DB::statement("
            UPDATE schedules 
            SET price = NULL 
            WHERE price = 0
               OR price IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - data cleanup is permanent
    }
};
