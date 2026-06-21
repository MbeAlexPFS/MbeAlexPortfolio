<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'pseudo' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'user',
            'is_verified' => true,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'pseudo' => 'MbeAlex',
            'email' => 'admin@mbealexportfolio.com',
            'role' => 'admin',
            'is_verified' => true,
            'headline' => 'Développeur Full Stack Créatif',
            'bio' => 'Développeur autodidacte passionné, actuellement en formation. Je crée des sites web, applications, logos, affiches et montages vidéo avec une attention particulière au design et à l\'expérience utilisateur.',
            'social_links' => [
                ['platform' => 'GitHub', 'url' => 'https://github.com/MbeAlex'],
                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/MbeAlex'],
            ],
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
