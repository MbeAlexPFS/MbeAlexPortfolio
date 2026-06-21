<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'pseudo', 'email', 'password', 'google_id', 'role',
    'is_verified', 'otp_code', 'otp_expires_at', 'avatar_url',
    'newsletter_articles', 'newsletter_polls', 'is_active',
    'headline', 'bio', 'social_links',
])]
#[Hidden(['password', 'remember_token', 'otp_code', 'otp_expires_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'otp_expires_at' => 'datetime',
            'newsletter_articles' => 'boolean',
            'newsletter_polls' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'social_links' => 'array',
        ];
    }

    public function avatarUrl(): Attribute
    {
        return Attribute::get(fn ($value) => $value
            ? Storage::url($value)
            : null);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
