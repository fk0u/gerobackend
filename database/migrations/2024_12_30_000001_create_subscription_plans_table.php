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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->json('features')->nullable(); // Store features as JSON
            $table->boolean('is_active')->default(true);
            $table->integer('max_orders_per_month')->nullable();
            $table->integer('max_tracking_locations')->nullable();
            $table->boolean('priority_support')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};