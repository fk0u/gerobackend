<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * SAFE MIGRATION - Hanya menambahkan kolom baru, tidak mengubah kolom existing
     * Mendukung multiple waste types dengan estimasi berat masing-masing
     * Backward compatible - field lama tetap ada
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkan (untuk safety)
            if (!Schema::hasColumn('schedules', 'waste_items')) {
                // Add JSON column for multiple waste items
                // Format: [{"waste_type":"organik","estimated_weight":5.5,"unit":"kg","notes":"optional"}]
                $table->json('waste_items')->nullable()->after('pickup_longitude');
            }
            
            if (!Schema::hasColumn('schedules', 'total_estimated_weight')) {
                // Add total estimated weight (auto-calculated from waste_items)
                // Berguna untuk query dan sorting
                $table->decimal('total_estimated_weight', 8, 2)->default(0.00)->after('waste_items');
                
                // Add index for better query performance
                $table->index('total_estimated_weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     * 
     * SAFE ROLLBACK - Hanya menghapus kolom yang ditambahkan migration ini
     * Tidak mempengaruhi tabel atau kolom lain
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Drop index first
            if (Schema::hasColumn('schedules', 'total_estimated_weight')) {
                $table->dropIndex(['total_estimated_weight']);
            }
            
            // Drop columns
            $table->dropColumn(['waste_items', 'total_estimated_weight']);
        });
    }
};