<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Frontend', 'Backend', 'DevOps', 'Design', 'Database', 'Mobile'];
        $frontend = ['HTML', 'CSS', 'JavaScript', 'React', 'Vue.js', 'Tailwind CSS', 'TypeScript'];
        $backend = ['PHP', 'Laravel', 'Node.js', 'Python', 'Java', 'Go', 'Ruby'];
        $devops = ['Docker', 'AWS', 'CI/CD', 'Linux', 'Nginx', 'Git'];
        $design = ['Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'UI/UX'];
        $database = ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite'];
        $mobile = ['Flutter', 'React Native', 'Swift', 'Kotlin'];

        $map = [
            'Frontend' => $frontend,
            'Backend' => $backend,
            'DevOps' => $devops,
            'Design' => $design,
            'Database' => $database,
            'Mobile' => $mobile,
        ];

        $category = fake()->randomElement($categories);
        $name = fake()->randomElement($map[$category]);

        return [
            'name' => $name,
            'level' => fake()->numberBetween(3, 5),
            'icon_url' => null,
            'category' => $category,
        ];
    }
}
