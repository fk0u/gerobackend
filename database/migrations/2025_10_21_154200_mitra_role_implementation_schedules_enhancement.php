<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: MITRA ROLE IMPLEMENTATION - Schedule Enhancement
 * 
 * Purpose: Add lifecycle tracking fields for Mitra schedule operations
 * Date: 2025-10-21 15:42:00
 * Feature: Complete Mitra workflow tracking (Accept → Start → Complete/Cancel)
 * 
 * Fields Added:
 * - started_at: When Mitra starts the pickup (status → in_progress)
 * - completed_at: When pickup is completed (status → completed)
 * - cancelled_at: When schedule is cancelled (status → cancelled)
 * - actual_weight: Actual waste weight collected (vs estimated)
 * 
 * Related Endpoints:
 * - POST /api/schedules/{id}/accept - Assign mitra (status → confirmed)
 * - POST /api/schedules/{id}/start - Start pickup (add started_at)
 * - POST /api/schedules/{id}/complete - Complete pickup (add completed_at + actual_weight)
 * - POST /api/schedules/{id}/cancel - Cancel schedule (add cancelled_at)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Check if columns don't exist before adding (idempotent migration)
            if (!Schema::hasColumn('schedules', 'started_at')) {
                $table->timestamp('started_at')->nullable()
                    ->after('scheduled_at')
                    ->comment('When Mitra started the pickup (status: in_progress)');
            }
            
            if (!Schema::hasColumn('schedules', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()
                    ->after('started_at')
                    ->comment('When pickup was completed (status: completed)');
            }
            
            if (!Schema::hasColumn('schedules', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()
                    ->after('completed_at')
                    ->comment('When schedule was cancelled (status: cancelled)');
            }
            
            if (!Schema::hasColumn('schedules', 'actual_weight')) {
                $table->decimal('actual_weight', 8, 2)->nullable()
                    ->after('total_estimated_weight')
                    ->comment('Actual weight collected by Mitra (kg)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'started_at')) {
                $table->dropColumn('started_at');
            }
            
            if (Schema::hasColumn('schedules', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            
            if (Schema::hasColumn('schedules', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            
            if (Schema::hasColumn('schedules', 'actual_weight')) {
                $table->dropColumn('actual_weight');
            }
        });
    }
};
