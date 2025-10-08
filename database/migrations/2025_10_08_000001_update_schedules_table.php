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
        Schema::table('schedules', function (Blueprint $table) {
            // Add new columns for improved schedule management
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('mitra_id')->nullable()->after('user_id');
            $table->string('service_type', 100)->nullable()->after('mitra_id');
            $table->text('pickup_address')->nullable()->after('service_type');
            $table->decimal('pickup_latitude', 10, 8)->nullable()->after('pickup_address');
            $table->decimal('pickup_longitude', 11, 8)->nullable()->after('pickup_latitude');
            $table->integer('estimated_duration')->nullable()->comment('Duration in minutes')->after('scheduled_at');
            $table->text('notes')->nullable()->after('estimated_duration');
            $table->enum('payment_method', ['cash', 'transfer', 'wallet'])->nullable()->after('notes');
            $table->decimal('price', 10, 2)->nullable()->after('payment_method');

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mitra_id')->references('id')->on('users')->onDelete('set null');

            // Update status enum to include more states
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mitra_id']);

            // Drop new columns
            $table->dropColumn([
                'user_id',
                'mitra_id',
                'service_type',
                'pickup_address',
                'pickup_latitude',
                'pickup_longitude',
                'estimated_duration',
                'notes',
                'payment_method',
                'price'
            ]);
        });
    }
};