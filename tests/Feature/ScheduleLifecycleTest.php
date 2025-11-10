<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;

class ScheduleLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_can_update_schedule()
    {
        $mitra = User::create([
            'name' => 'Mitra Test',
            'email' => 'mitra@example.com',
            'password' => bcrypt('secret'),
            'role' => 'mitra',
        ]);

        $schedule = Schedule::create([
            'user_id' => $mitra->id,
            'service_type' => 'pickup_sampah_campuran',
            'pickup_address' => 'Jl Test',
            'pickup_latitude' => -0.5,
            'pickup_longitude' => 117.15,
            'scheduled_at' => now()->addDay(),
            'status' => 'pending',
            'title' => 'Test',
            'description' => 'Test desc',
            'latitude' => -0.5,
            'longitude' => 117.15,
        ]);

        $this->actingAs($mitra, 'sanctum')
            ->patchJson("/api/schedules/{$schedule->id}", [
                'notes' => 'Updated by mitra',
                'status' => 'confirmed',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_mitra_can_complete_schedule()
    {
        $mitra = User::create([
            'name' => 'Mitra Test',
            'email' => 'mitra2@example.com',
            'password' => bcrypt('secret'),
            'role' => 'mitra',
        ]);

        $schedule = Schedule::create([
            'user_id' => $mitra->id,
            'service_type' => 'pickup_sampah_campuran',
            'pickup_address' => 'Jl Test',
            'pickup_latitude' => -0.5,
            'pickup_longitude' => 117.15,
            'scheduled_at' => now()->subHour(),
            'status' => 'in_progress',
            'title' => 'Test',
            'description' => 'Test desc',
            'latitude' => -0.5,
            'longitude' => 117.15,
        ]);

        $this->actingAs($mitra, 'sanctum')
            ->postJson("/api/schedules/{$schedule->id}/complete", [
                'completion_notes' => 'Done successfully',
                'actual_duration' => 30,
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'status' => 'completed',
        ]);
    }

    public function test_mitra_can_cancel_schedule()
    {
        $mitra = User::create([
            'name' => 'Mitra Test',
            'email' => 'mitra3@example.com',
            'password' => bcrypt('secret'),
            'role' => 'mitra',
        ]);

        $schedule = Schedule::create([
            'user_id' => $mitra->id,
            'service_type' => 'pickup_sampah_campuran',
            'pickup_address' => 'Jl Test',
            'pickup_latitude' => -0.5,
            'pickup_longitude' => 117.15,
            'scheduled_at' => now()->addHour(),
            'status' => 'pending',
            'title' => 'Test',
            'description' => 'Test desc',
            'latitude' => -0.5,
            'longitude' => 117.15,
        ]);

        $this->actingAs($mitra, 'sanctum')
            ->postJson("/api/schedules/{$schedule->id}/cancel", [
                'cancellation_reason' => 'Customer requested',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'status' => 'cancelled',
        ]);
    }
}
