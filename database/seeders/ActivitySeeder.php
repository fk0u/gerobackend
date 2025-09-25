<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityDetail;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear old
        ActivityDetail::query()->delete();
        Activity::query()->delete();

        $now = now();
        $months = [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
        ];

        // Helper to build date string like Dart mock (not stored; we store scheduled_at)
        $randomAddress = fn($i) => 'Jl. Contoh No. '.($i+1).', Jakarta';

        // Completed activities (C prefix), older first
        for ($i = 0; $i < 10; $i++) {
            $daysAgo = random_int(1, 30);
            $hour = 8 + random_int(0, 10); // 8..18
            $minute = random_int(0, 59);
            $dt = $now->copy()->subDays($daysAgo)->setTime($hour, $minute);

            $activity = Activity::create([
                'code' => 'C'.(100 + $i),
                'title' => 'Pengambilan Selesai',
                'address' => $randomAddress($i),
                'status' => 'Selesai',
                'is_active' => false,
                'scheduled_at' => $dt,
                'user_id' => null,
                'mitra_id' => null,
            ]);

            // Add 1-4 random trash details
            $types = [
                ['type' => 'Organik', 'points_per_kg' => 5, 'icon' => 'assets/ic_trashh.png'],
                ['type' => 'Plastik', 'points_per_kg' => 10, 'icon' => 'assets/ic_trash.png'],
                ['type' => 'Kertas', 'points_per_kg' => 8, 'icon' => 'assets/ic_trash.png'],
                ['type' => 'Kaca', 'points_per_kg' => 15, 'icon' => 'assets/ic_trash.png'],
                ['type' => 'Logam', 'points_per_kg' => 25, 'icon' => 'assets/ic_trash.png'],
                ['type' => 'Elektronik', 'points_per_kg' => 30, 'icon' => 'assets/ic_trash.png'],
                ['type' => 'Lainnya', 'points_per_kg' => 3, 'icon' => 'assets/ic_trash.png'],
            ];

            shuffle($types);
            $take = random_int(1, 4);
            for ($k = 0; $k < $take; $k++) {
                $t = $types[$k];
                $weight = random_int(1, 10);
                $points = $weight * $t['points_per_kg'];
                ActivityDetail::create([
                    'activity_id' => $activity->id,
                    'type' => $t['type'],
                    'weight' => $weight,
                    'points' => $points,
                    'icon' => $t['icon'],
                ]);
            }
        }

        // Active activities (A prefix), newest first
        for ($i = 0; $i < 5; $i++) {
            $daysAgo = random_int(1, 30);
            $hour = 8 + random_int(0, 10);
            $minute = random_int(0, 59);
            $dt = $now->copy()->subDays($daysAgo)->setTime($hour, $minute);

            $status = (random_int(0, 1) === 1) ? 'Dijadwalkan' : 'Menuju Lokasi';

            Activity::create([
                'code' => 'A'.(100 + $i),
                'title' => 'Pengambilan Dijadwalkan',
                'address' => $randomAddress($i+50),
                'status' => $status,
                'is_active' => true,
                'scheduled_at' => $dt,
                'user_id' => null,
                'mitra_id' => null,
            ]);
        }
    }
}
