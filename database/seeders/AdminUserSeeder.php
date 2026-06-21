<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            "pseudo" => "MbeAlex",
            "email" => "admin@mbealexportfolio.com",
            "password" => Hash::make("89218921mbe007007portfolio@@"),
            "role" => "admin",
            "is_verified" => true,
            "is_active" => true,
            "headline" => "Développeur Junior Full Stack et Design",
            "bio" =>
                'Développeur autodidacte passionné, actuellement en formation. Je crée des sites web, applications, logos, affiches et montages vidéo avec une attention particulière au design et à l\'expérience utilisateur.',
            "social_links" => [
                [
                    "platform" => "GitHub",
                    "url" => "https://github.com/MbeAlexPFS",
                ],
                [
                    "platform" => "Youtube",
                    "url" => "https://www.youtube.com/@AlexMbeDev",
                ],
            ],
        ]);
    }
}
