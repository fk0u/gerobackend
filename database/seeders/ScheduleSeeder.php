<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schedule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $mitraId = User::where('role', 'mitra')->value('id');

        Schedule::create([
            'title' => 'Pickup Station A',
            'description' => 'Collect at point A',
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'status' => 'pending',
            'assigned_to' => $mitraId,
            'scheduled_at' => now()->addDay(),
        ]);

        Schedule::create([
            'title' => 'Pickup Station B',
            'description' => 'Collect at point B',
            'latitude' => -6.1753924,
            'longitude' => 106.8271528,
            'status' => 'in_progress',
            'assigned_to' => $mitraId,
            'scheduled_at' => now(),
        ]);
    }
}
