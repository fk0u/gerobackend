<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('AddScheduleAdditionalFields')) {
    class AddScheduleAdditionalFields extends Migration
    {
        public function up(): void
        {
            if (!Schema::hasTable('schedules')) {
                return;
            }

            Schema::table('schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('schedules', 'waste_type')) {
                    $table->string('waste_type', 50)->nullable()->after('notes');
                }

                if (!Schema::hasColumn('schedules', 'estimated_weight')) {
                    $table->decimal('estimated_weight', 8, 2)->nullable()->after('waste_type');
                }

                if (!Schema::hasColumn('schedules', 'contact_name')) {
                    $table->string('contact_name')->nullable()->after('pickup_address');
                }

                if (!Schema::hasColumn('schedules', 'contact_phone')) {
                    $table->string('contact_phone', 20)->nullable()->after('contact_name');
                }

                if (!Schema::hasColumn('schedules', 'is_paid')) {
                    $table->boolean('is_paid')->default(false)->after('status');
                }

                if (!Schema::hasColumn('schedules', 'amount')) {
                    $table->decimal('amount', 10, 2)->nullable()->after('is_paid');
                }

                if (!Schema::hasColumn('schedules', 'frequency')) {
                    $table->enum('frequency', ['once', 'daily', 'weekly', 'biweekly', 'monthly'])->default('once')->after('scheduled_at');
                }
            });
        }

        public function down(): void
        {
            if (!Schema::hasTable('schedules')) {
                return;
            }

            Schema::table('schedules', function (Blueprint $table) {
                $columns = [
                    'waste_type',
                    'estimated_weight',
                    'contact_name',
                    'contact_phone',
                    'is_paid',
                    'amount',
                    'frequency',
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
