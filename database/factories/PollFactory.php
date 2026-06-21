<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'is_active' => true,
            'start_date' => fake()->dateTimeBetween('-1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
        ];
    }
}
