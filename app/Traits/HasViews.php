<?php

namespace App\Traits;

use App\Models\View;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Request;

trait HasViews
{
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function viewsCount(): int
    {
        return $this->views()->count();
    }

    public function recordView(): void
    {
        $ip = Request::ip();
        $userId = auth()->id();

        $existing = $this->views()
            ->where(function ($q) use ($ip, $userId) {
                $q->where('ip_address', $ip);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })
            ->exists();

        if (! $existing) {
            $this->views()->create([
                'user_id' => $userId,
                'ip_address' => $ip,
            ]);
        }
    }
}
