<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JobProgress extends Model
{
    protected $fillable = ['type', 'reference_type', 'reference_id', 'total', 'completed', 'status', 'error'];

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function percentage(): int
    {
        if ($this->total === 0) {
            return 0;
        }

        return (int) round(($this->completed / $this->total) * 100);
    }
}
