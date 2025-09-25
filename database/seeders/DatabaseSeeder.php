<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            UserAndMitraSeeder::class,
            ServiceSeeder::class,
            ScheduleSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            ActivitySeeder::class,
            TrackingSeeder::class,
            BalanceSeeder::class,
            NotificationSeeder::class,
            ChatSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
