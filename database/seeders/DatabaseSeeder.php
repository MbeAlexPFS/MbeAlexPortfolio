<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([AdminUserSeeder::class]);

        $skills = Skill::factory(12)->create();

        $tags = Tag::factory(10)->create();

        $projects = Project::factory(6)->create();
        foreach ($projects as $project) {
            $project
                ->skills()
                ->attach($skills->random(rand(2, 4))->pluck('id'));
            $project->tags()->attach($tags->random(rand(2, 3))->pluck('id'));
        }

        ContactMessage::factory(5)->create();
    }
}
