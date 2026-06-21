<?php

namespace Database\Factories;

use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'text' => fake()->word(),
            'order_index' => fake()->numberBetween(0, 10),
        ];
    }
}
