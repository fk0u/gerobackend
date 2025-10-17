<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Clean invalid data first
        DB::statement("DELETE FROM trackings WHERE latitude IS NULL OR latitude = '' OR longitude IS NULL OR longitude = ''");
        
        // Change VARCHAR to DECIMAL (nullable for safety)
        Schema::table('trackings', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->decimal('speed', 8, 2)->nullable()->change();
            $table->decimal('heading', 5, 2)->nullable()->change();
        });
        
        // Delete any remaining invalid entries after type change
        DB::statement("DELETE FROM trackings WHERE latitude = 0 AND longitude = 0");
    }

    public function down(): void
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->string('latitude', 32)->change();
            $table->string('longitude', 32)->change();
            $table->float('speed')->nullable()->change();
            $table->float('heading')->nullable()->change();
        });
    }
};
