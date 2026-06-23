<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {}

    public function handle(): void
    {
        $this->project->refresh();

        if ($this->project->thumbnail_status !== 'pending') {
            return;
        }

        $this->project->update(['thumbnail_status' => 'processing']);

        $this->project->refresh();

        if ($this->project->thumbnail_status !== 'processing') {
            return;
        }

        $screenshotUrl = 'https://mini.s-shot.ru/1280x1024/PNG/1024/?'.urlencode($this->project->live_url);

        $response = Http::timeout(30)->get($screenshotUrl);

        if ($response->failed()) {
            $this->project->update(['thumbnail_status' => 'failed']);

            return;
        }

        $filename = $this->project->id.'_thumbnail.png';

        Storage::disk('public')->put('projects/'.$filename, $response->body());

        if ($this->project->image_url && str_starts_with($this->project->image_url, '/storage/projects/')) {
            $oldPath = str_replace('/storage/', '', $this->project->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $this->project->update([
            'image_url' => Storage::url('projects/'.$filename),
            'thumbnail_status' => 'completed',
        ]);
    }
}
