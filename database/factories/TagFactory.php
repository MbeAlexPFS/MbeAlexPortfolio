<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laravel', 'Vue.js', 'React', 'PHP', 'JavaScript',
            'TypeScript', 'Tailwind CSS', 'MySQL', 'Docker',
            'Figma', 'UI Design', 'API', 'Full Stack', 'Mobile',
            'DevOps', 'SEO', 'Performance', 'Accessibility',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
