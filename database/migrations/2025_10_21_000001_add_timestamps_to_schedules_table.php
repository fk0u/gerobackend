<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('AddTimestampsToSchedulesTable')) {
    class AddTimestampsToSchedulesTable extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                // Add timestamps if they don't exist
                if (!Schema::hasColumn('schedules', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (!Schema::hasColumn('schedules', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }

                // Add completed_at for tracking completion time
                if (!Schema::hasColumn('schedules', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()
                        ->comment('Timestamp when schedule was marked as completed');
                }

                // Add cancelled_at for tracking cancellation time
                if (!Schema::hasColumn('schedules', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()
                        ->comment('Timestamp when schedule was cancelled');
                }

                // Add confirmed_at for tracking confirmation time
                if (!Schema::hasColumn('schedules', 'confirmed_at')) {
                    $table->timestamp('confirmed_at')->nullable()
                        ->comment('Timestamp when schedule was confirmed by mitra');
                }

                // Add started_at for tracking when pickup started
                if (!Schema::hasColumn('schedules', 'started_at')) {
                    $table->timestamp('started_at')->nullable()
                        ->comment('Timestamp when pickup was started');
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                $columns = ['completed_at', 'cancelled_at', 'confirmed_at', 'started_at'];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('schedules', $column)) {
                        $table->dropColumn($column);
                    }
                }

                // Note: We don't drop created_at and updated_at as they might be used by other parts
            });
        }
    }
}
