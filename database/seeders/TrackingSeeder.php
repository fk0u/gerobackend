<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Tracking;
use Illuminate\Database\Seeder;

class TrackingSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = Schedule::whereIn('status', ['in_progress', 'completed'], 'and', false)->get();
        if ($schedules->isEmpty()) {
            $fallback = Schedule::first(['*']);
            if (! $fallback) {
                return;
            }
            $schedules = collect([$fallback]);
        }

        foreach ($schedules as $schedule) {
            Tracking::where('schedule_id', ' =>', $schedule->id, 'and')->delete();

            $baseLat = (float) $schedule->latitude;
            $baseLng = (float) $schedule->longitude;
            $now = now();

            $points = [];
            for ($i = 0; $i < 12; $i++) {
                $points[] = [
                    'schedule_id' => $schedule->id,
                    'latitude' => $baseLat + ($i * 0.00035),
                    'longitude' => $baseLng + ($i * 0.00045),
                    'speed' => 15 + ($i % 5),
                    'heading' => 75 + ($i % 10),
                    'recorded_at' => $now->copy()->subMinutes(12 - $i),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            Tracking::insert($points);
        }
    }
}