<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['unique_choice', 'multiple_choice', 'text_short']);

        return [
            'text' => fake()->sentence(8),
            'type' => $type,
            'is_required' => fake()->boolean(80),
            'order_index' => fake()->numberBetween(0, 10),
        ];
    }

    public function scale(int $min = 1, int $max = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'text' => "Notez de {$min} à {$max}",
            'type' => 'unique_choice',
        ]);
    }

    public function textShort(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'text_short',
        ]);
    }
}
