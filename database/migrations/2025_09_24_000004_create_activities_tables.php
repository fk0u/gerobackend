<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., C101, A102
            $table->string('title'); // 'Pengambilan Selesai' / 'Pengambilan Dijadwalkan'
            $table->string('address');
            $table->string('status'); // 'Selesai', 'Dijadwalkan', 'Menuju Lokasi'
            $table->boolean('is_active')->default(false); // active = ongoing/upcoming
            $table->timestamp('scheduled_at')->nullable(); // original dateTime
            $table->unsignedBigInteger('user_id')->nullable(); // end user
            $table->unsignedBigInteger('mitra_id')->nullable(); // mitra
            $table->timestamps();
        });

        Schema::create('activity_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('type'); // trash type
            $table->integer('weight'); // kg
            $table->integer('points');
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_details');
        Schema::dropIfExists('activities');
    }
};
