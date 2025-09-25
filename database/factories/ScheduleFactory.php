<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'latitude' => $this->faker->latitude(-7, -6),
            'longitude' => $this->faker->longitude(106, 108),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'done']),
            'assigned_to' => 1,
            'scheduled_at' => now()->addDays(rand(0, 5)),
        ];
    }
}
