<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create additional_wastes table
        Schema::create('additional_wastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->string('waste_type', 50);
            $table->decimal('estimated_weight', 8, 2)->nullable(); // in kg
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for better query performance
            $table->index('schedule_id');
            $table->index('waste_type');
        });

        // Add new fields to schedules table for better management
        Schema::table('schedules', function (Blueprint $table) {
            // Add waste type and weight for main waste
            $table->string('waste_type', 50)->nullable()->after('notes');
            $table->decimal('estimated_weight', 8, 2)->nullable()->after('waste_type');
            
            // Add contact information
            $table->string('contact_name')->nullable()->after('pickup_address');
            $table->string('contact_phone', 20)->nullable()->after('contact_name');
            
            // Add payment tracking
            $table->boolean('is_paid')->default(false)->after('status');
            $table->decimal('amount', 10, 2)->nullable()->after('is_paid');
            
            // Add frequency for recurring schedules
            $table->enum('frequency', ['once', 'daily', 'weekly', 'biweekly', 'monthly'])->default('once')->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop additional_wastes table
        Schema::dropIfExists('additional_wastes');
        
        // Remove added columns from schedules table
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'waste_type',
                'estimated_weight',
                'contact_name',
                'contact_phone',
                'is_paid',
                'amount',
                'frequency'
            ]);
        });
    }
};
