<?php

namespace App\Jobs;

use App\Models\JobProgress;
use App\Models\Project;
use App\Services\GitHubService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Str;

class SyncGitHubJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobProgress $progress,
        public array $selected,
    ) {}

    public function handle(GitHubService $github): void
    {
        $this->progress->update(['status' => 'processing']);

        $selected = array_filter($this->selected, fn ($a) => ! empty($a['import']));
        $total = count($selected);
        $this->progress->update(['total' => $total]);

        if ($total === 0) {
            $this->progress->update(['status' => 'completed', 'error' => 'Aucun projet sélectionné.']);

            return;
        }

        $repos = collect($github->fetchPublicRepos());

        $candidates = [];
        foreach ($selected as $repoId => $action) {
            $repo = $repos->firstWhere('id', (int) $repoId);
            if ($repo) {
                $candidates[(int) $repoId] = [
                    'repo' => $repo,
                    'replace' => ! empty($action['replace']),
                ];
            }
        }

        $repoIds = array_keys($candidates);
        $fullNames = array_map(fn ($c) => $c['repo']['full_name'], $candidates);
        $fullNames = array_values($fullNames);

        $contentsResponses = Http::pool(fn (Pool $pool) => array_map(
            fn ($name) => $pool->withHeaders(['Accept' => 'application/vnd.github.v3+json'])
                ->asJson()
                ->get("https://api.github.com/repos/{$name}/contents"),
            $fullNames,
        ));

        $valid = [];
        foreach ($repoIds as $i => $repoId) {
            $response = $contentsResponses[$i] ?? null;

            if (! $response || $response->failed()) {
                $this->progress->increment('completed');

                continue;
            }

            $files = $response->json();
            $hasIndex = false;
            $hasPhp = false;

            foreach ($files as $file) {
                if ($file['name'] === 'index.html') {
                    $hasIndex = true;
                }
                if (str_ends_with($file['name'], '.php') || ($file['type'] === 'dir' && in_array($file['name'], ['vendor', 'node_modules']))) {
                    $hasPhp = true;
                }
            }

            if ($hasIndex && ! $hasPhp) {
                $valid[] = $candidates[$repoId];
            } else {
                $this->progress->increment('completed');
            }
        }

        $readmePool = [];
        foreach ($valid as $item) {
            if (empty($item['repo']['description'])) {
                $readmePool[] = $item['repo']['full_name'];
            }
        }

        $readmeResponses = [];

        if ($readmePool !== []) {
            $readmeResponses = Http::pool(fn (Pool $pool) => array_map(
                fn ($name) => $pool->withHeaders(['Accept' => 'application/vnd.github.v3.raw'])
                    ->get("https://api.github.com/repos/{$name}/readme"),
                $readmePool,
            ));
        }

        $imported = 0;
        $skipped = 0;
        $readmeIdx = 0;

        foreach ($valid as $item) {
            $repo = $item['repo'];
            $shouldReplace = $item['replace'];
            $fullName = $repo['full_name'];
            $repoId = $repo['id'];

            $existing = Project::where('github_repo_id', $repoId)->first();

            if ($existing && ! $shouldReplace) {
                $skipped++;
                $this->progress->increment('completed');

                continue;
            }

            $defaultBranch = $repo['default_branch'] ?? 'main';

            $description = $repo['description'] ?? '';

            if ($description === '') {
                $resp = $readmeResponses[$readmeIdx] ?? null;

                if ($resp && $resp->successful()) {
                    $description = Str::limit($resp->body(), 300);
                }
                $readmeIdx++;
            }

            $data = [
                'title' => $repo['name'],
                'description' => $description,
                'type' => 'web_static',
                'github_url' => $repo['html_url'],
                'live_url' => 'https://htmlpreview.github.io/?https://github.com/'.$fullName.'/blob/'.$defaultBranch.'/index.html',
                'github_repo_id' => $repoId,
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                Project::create($data);
            }

            $imported++;
            $this->progress->increment('completed');
        }

        $parts = [];

        if ($imported > 0) {
            $parts[] = "{$imported} synchronisé(s)";
        }
        if ($skipped > 0) {
            $parts[] = "{$skipped} ignoré(s)";
        }

        $message = $parts ? implode(', ', $parts).'.' : 'Aucun traitement.';

        $this->progress->update([
            'status' => 'completed',
            'error' => $message,
        ]);
    }
}
