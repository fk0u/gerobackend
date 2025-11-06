<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!class_exists('AddMultipleWasteToSchedulesTable')) {
    class AddMultipleWasteToSchedulesTable extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                // Add support for multiple waste types selection
                if (!Schema::hasColumn('schedules', 'waste_types')) {
                    $table->json('waste_types')->nullable()->after('service_type')
                        ->comment('JSON array of selected waste types: ["organik", "anorganik", "daur_ulang", "b3"]');
                }

                if (!Schema::hasColumn('schedules', 'estimated_weight')) {
                    $table->decimal('estimated_weight', 8, 2)->nullable()->after('waste_types')
                        ->comment('Estimated total weight in kg');
                }

                if (!Schema::hasColumn('schedules', 'actual_weight')) {
                    $table->decimal('actual_weight', 8, 2)->nullable()->after('estimated_weight')
                        ->comment('Actual weight collected in kg');
                }

                if (!Schema::hasColumn('schedules', 'pickup_image')) {
                    $table->string('pickup_image')->nullable()->after('actual_weight')
                        ->comment('Image uploaded before pickup');
                }

                if (!Schema::hasColumn('schedules', 'completion_image')) {
                    $table->string('completion_image')->nullable()->after('pickup_image')
                        ->comment('Image uploaded after completion');
                }
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('schedules', function (Blueprint $table) {
                $columns = ['waste_types', 'estimated_weight', 'actual_weight', 'pickup_image', 'completion_image'];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('schedules', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
}