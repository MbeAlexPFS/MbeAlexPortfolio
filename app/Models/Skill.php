<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'level', 'icon_url', 'category'])]
class Skill extends \Illuminate\Database\Eloquent\Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
