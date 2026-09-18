<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'description', 'type', 'image_url', 'github_url', 'live_url', 'github_repo_id', 'thumbnail_status'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    public $timestamps = false;

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function getGithubPagesUrlAttribute(): ?string
    {
        if (empty($this->github_url)) {
            return $this->live_url;
        }

        $path = trim(parse_url($this->github_url, PHP_URL_PATH) ?? '', '/');

        if ($path === '') {
            return $this->live_url;
        }

        [$owner, $repo] = explode('/', $path, 2);

        if (str_ends_with($repo, '.github.io')) {
            return 'https://'.$repo.'/';
        }

        return 'https://'.$owner.'.github.io/'.$repo.'/';
    }
}
