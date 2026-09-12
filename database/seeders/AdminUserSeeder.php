<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'pseudo' => 'MbeAlex',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
            'headline' => 'Développeur Junior Full Stack',
            'bio' => 'Développeur autodidacte passionné, actuellement en formation. Je conçois des applications web modernes, performantes et élégantes avec Laravel, Vue.js et Tailwind CSS, en accordant une attention particulière à l\'expérience utilisateur.',
            'social_links' => [
                [
                    'platform' => 'GitHub',
                    'url' => 'https://github.com/MbeAlexPFS',
                ],
                [
                    'platform' => 'Youtube',
                    'url' => 'https://www.youtube.com/@AlexMbeDev',
                ],
                [
                    'platform' => 'X',
                    'url' => 'https://x.com/alexmbepfs',
                ],
            ],
        ]);
    }
}
