<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Poll;
use App\Models\Project;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([AdminUserSeeder::class]);

        $admin = User::where('role', 'admin')->first();

        $users = User::factory(10)->create();

        $skills = Skill::factory(12)->create();

        $tags = Tag::factory(10)->create();

        $projects = Project::factory(6)->create();
        foreach ($projects as $project) {
            $project
                ->skills()
                ->attach($skills->random(rand(2, 4))->pluck('id'));
            $project->tags()->attach($tags->random(rand(2, 3))->pluck('id'));
        }

        $articles = Article::factory(8)
            ->published()
            ->create([
                'user_id' => $admin->id,
            ]);
        foreach ($articles as $article) {
            $article->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
        }

        foreach ($articles->take(4) as $article) {
            Comment::factory(3)->create([
                'article_id' => $article->id,
                'user_id' => $users->random()->id,
                'is_published' => true,
            ]);
        }

        Comment::factory(3)->create([
            'article_id' => $articles->random()->id,
            'user_id' => $users->random()->id,
            'is_published' => false,
        ]);

        ContactMessage::factory(5)->create();

        $poll = Poll::factory()->create([
            'title' => 'Que pensez-vous de mon portfolio ?',
        ]);

        $q1 = Question::factory()->create([
            'poll_id' => $poll->id,
            'text' => 'Comment avez-vous trouvé mon portfolio ?',
            'type' => 'unique_choice',
            'order_index' => 0,
        ]);
        foreach (
            ['Excellent', 'Très bien', 'Bien', 'Moyen', 'À améliorer'] as $i => $opt
        ) {
            QuestionOption::factory()->create([
                'question_id' => $q1->id,
                'text' => $opt,
                'order_index' => $i,
            ]);
        }

        $q2 = Question::factory()->create([
            'poll_id' => $poll->id,
            'text' => 'Quelles sections avez-vous consultées ?',
            'type' => 'multiple_choice',
            'order_index' => 1,
        ]);
        foreach (['Projets', 'Blog', 'Compétences', 'Contact'] as $i => $opt) {
            QuestionOption::factory()->create([
                'question_id' => $q2->id,
                'text' => $opt,
                'order_index' => $i,
            ]);
        }

        $q3 = Question::factory()
            ->textShort()
            ->create([
                'poll_id' => $poll->id,
                'text' => 'Avez-vous des suggestions d\'amélioration ?',
                'order_index' => 2,
            ]);

        $poll2 = Poll::factory()->create([
            'title' => 'Technologies préférées',
        ]);

        $q4 = Question::factory()
            ->scale(1, 5)
            ->create([
                'poll_id' => $poll2->id,
                'text' => 'Évaluez Laravel de 1 à 5',
                'order_index' => 0,
            ]);
        foreach (['1', '2', '3', '4', '5'] as $i => $opt) {
            QuestionOption::factory()->create([
                'question_id' => $q4->id,
                'text' => $opt,
                'order_index' => $i + 1,
            ]);
        }
    }
}
