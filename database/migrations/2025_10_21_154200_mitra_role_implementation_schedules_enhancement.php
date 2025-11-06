<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('MitraRoleImplementationSchedulesEnhancement')) {
    class MitraRoleImplementationSchedulesEnhancement extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                // Add mitra assignment fields
                if (!Schema::hasColumn('schedules', 'assigned_at')) {
                    $table->timestamp('assigned_at')->nullable()
                        ->comment('When mitra was assigned to this schedule');
                }

                if (!Schema::hasColumn('schedules', 'assigned_by')) {
                    $table->foreignId('assigned_by')->nullable()->constrained('users')
                        ->comment('Admin/system user who assigned this mitra');
                }

                // Add mitra acceptance fields
                if (!Schema::hasColumn('schedules', 'accepted_at')) {
                    $table->timestamp('accepted_at')->nullable()
                        ->comment('When mitra accepted the schedule');
                }

                if (!Schema::hasColumn('schedules', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()
                        ->comment('When mitra rejected the schedule');
                }

                if (!Schema::hasColumn('schedules', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()
                        ->comment('Reason why mitra rejected the schedule');
                }

                // Add completion details
                if (!Schema::hasColumn('schedules', 'completion_notes')) {
                    $table->text('completion_notes')->nullable()
                        ->comment('Notes from mitra upon completion');
                }

                if (!Schema::hasColumn('schedules', 'actual_duration')) {
                    $table->integer('actual_duration')->nullable()
                        ->comment('Actual duration in minutes');
                }

                // Add rating fields
                if (!Schema::hasColumn('schedules', 'mitra_rating')) {
                    $table->decimal('mitra_rating', 3, 2)->nullable()
                        ->comment('Rating given to mitra for this schedule (1-5)');
                }

                if (!Schema::hasColumn('schedules', 'user_rating')) {
                    $table->decimal('user_rating', 3, 2)->nullable()
                        ->comment('Rating given to user for this schedule (1-5)');
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                $columns = [
                    'assigned_at',
                    'assigned_by',
                    'accepted_at',
                    'rejected_at',
                    'rejection_reason',
                    'completion_notes',
                    'actual_duration',
                    'mitra_rating',
                    'user_rating'
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('schedules', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
}
