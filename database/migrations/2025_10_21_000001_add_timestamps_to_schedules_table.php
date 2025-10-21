<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan timestamp untuk tracking lifecycle schedule
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('schedules', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('scheduled_at');
            }
            
            if (!Schema::hasColumn('schedules', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
            
            if (!Schema::hasColumn('schedules', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            }
            
            // Add actual_weight column for recording actual waste weight after completion
            if (!Schema::hasColumn('schedules', 'actual_weight')) {
                $table->decimal('actual_weight', 8, 2)->nullable()->after('total_estimated_weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at', 'cancelled_at', 'actual_weight']);
        });
    }
};
