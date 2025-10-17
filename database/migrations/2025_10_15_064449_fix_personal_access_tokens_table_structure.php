<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the corrupt table
        Schema::dropIfExists('personal_access_tokens');
        
        // Recreate with correct structure (from Laravel Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->morphs('tokenable'); // tokenable_type VARCHAR, tokenable_id BIGINT
            $table->string('name'); // VARCHAR(255)
            $table->string('token', 64)->unique(); // VARCHAR(64) UNIQUE
            $table->text('abilities')->nullable(); // TEXT
            $table->timestamp('last_used_at')->nullable(); // TIMESTAMP
            $table->timestamp('expires_at')->nullable()->index(); // TIMESTAMP with INDEX
            $table->timestamps(); // created_at, updated_at TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot revert - data would be lost anyway
        Schema::dropIfExists('personal_access_tokens');
    }
};
