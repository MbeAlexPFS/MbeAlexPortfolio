<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'pseudo' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => (static::$password ??= Hash::make('password')),
            'role' => 'user',
            'is_active' => true,
            'is_verified' => true,
        ];
    }
}
