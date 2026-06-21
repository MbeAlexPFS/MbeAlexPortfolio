<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug'])]
class Tag extends \Illuminate\Database\Eloquent\Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }
}
