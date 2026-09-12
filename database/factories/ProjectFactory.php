<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'type' => 'web_static',
            'image_url' => null,
            'github_url' => 'https://github.com/MbeAlex/'.fake()->slug(),
            'live_url' => 'https://'.fake()->domainName(),
        ];
    }
}
