<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GitHubService
{
    protected string $username;

    protected array $repoCache = [];

    public function __construct()
    {
        $this->username = $this->resolveUsername();
    }

    protected function resolveUsername(): string
    {
        $admin = User::where('role', 'admin')->first();

        if ($admin && $admin->social_links) {
            foreach ($admin->social_links as $link) {
                if (str_contains($link['url'] ?? '', 'github.com')) {
                    $parts = explode('/', parse_url($link['url'], PHP_URL_PATH) ?? '');

                    return $parts[1] ?? config('services.github.username', '');
                }
            }
        }

        return config('services.github.username', '');
    }

    public function fetchPublicRepos(): array
    {
        $page = 1;
        $repos = [];

        do {
            $response = Http::withHeaders(['Accept' => 'application/vnd.github.v3+json'])
                ->get("https://api.github.com/users/{$this->username}/repos", [
                    'type' => 'public',
                    'per_page' => 100,
                    'page' => $page,
                    'sort' => 'updated',
                ]);

            if ($response->failed()) {
                break;
            }

            $batch = $response->json();
            $repos = array_merge($repos, $batch);
            $page++;
        } while (count($batch) === 100);

        return $repos;
    }

    public function isStaticHtml(string $fullName): bool
    {
        if (isset($this->repoCache[$fullName])) {
            return $this->repoCache[$fullName];
        }

        $response = Http::withHeaders(['Accept' => 'application/vnd.github.v3+json'])
            ->get("https://api.github.com/repos/{$fullName}/contents");

        if ($response->failed()) {
            $this->repoCache[$fullName] = false;

            return false;
        }

        $files = $response->json();
        $hasIndex = false;
        $hasPhp = false;

        foreach ($files as $file) {
            if ($file['name'] === 'index.html') {
                $hasIndex = true;
            }
            if (str_ends_with($file['name'], '.php')) {
                $hasPhp = true;
            }
            if ($file['type'] === 'dir' && in_array($file['name'], ['vendor', 'node_modules'])) {
                $hasPhp = true;
            }
        }

        $result = $hasIndex && ! $hasPhp;
        $this->repoCache[$fullName] = $result;

        return $result;
    }

    public function getReadmeText(string $fullName): ?string
    {
        $response = Http::withHeaders(['Accept' => 'application/vnd.github.v3.raw'])
            ->get("https://api.github.com/repos/{$fullName}/readme");

        if ($response->failed()) {
            return null;
        }

        return $response->body();
    }

    public function getDescription(array $repo, string $fullName): string
    {
        if (! empty($repo['description'])) {
            return $repo['description'];
        }

        $readme = $this->getReadmeText($fullName);

        if ($readme) {
            return Str::limit($readme, 300);
        }

        return '';
    }
}
