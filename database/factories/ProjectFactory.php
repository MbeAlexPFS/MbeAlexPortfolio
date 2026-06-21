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
        $types = ['web_static', 'web_dynamic', 'web_live', 'design', 'affiche', 'logo', 'montage_video'];

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement($types),
            'image_url' => null,
            'github_url' => 'https://github.com/MbeAlex/' . fake()->slug(),
            'live_url' => 'https://' . fake()->domainName(),
        ];
    }
}
