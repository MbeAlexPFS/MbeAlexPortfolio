<?php

namespace App\Http\Controllers;

use App\Models\JobProgress;
use App\Models\Project;
use App\Models\Skill;
use App\Services\GitHubService;
use App\Services\ImageService;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with(['skills'])
            ->where('type', 'web_static')
            ->latest('created_at')
            ->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        return view('projects.show', [
            'project' => $project->load(['skills']),
        ]);
    }

    public function adminIndex(): View
    {
        $projects = Project::with(['skills'])
            ->where('type', 'web_static')
            ->latest('created_at')
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $skills = Skill::all();

        return view('admin.projects.form', compact('skills'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,ogg',
                'max:10240',
            ],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        $data['type'] = 'web_static';

        if ($request->hasFile('image')) {
            $data['image_url'] = ImageService::upload($request->file('image'), 'projects');
        }

        $project = Project::create($data);

        if (! empty($data['skills'])) {
            $project->skills()->attach($data['skills']);
        }

        return to_route('admin.projects.index')->with(
            'success',
            'Projet créé avec succès.',
        );
    }

    public function edit(Project $project): View
    {
        $project->load(['skills']);
        $skills = Skill::all();

        return view('admin.projects.form', compact('project', 'skills'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,ogg',
                'max:10240',
            ],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        $data['type'] = 'web_static';

        if ($request->hasFile('image')) {
            ImageService::delete($project->image_url);

            $data['image_url'] = ImageService::upload($request->file('image'), 'projects');
        }

        $project->update($data);

        $project->skills()->sync($data['skills'] ?? []);

        return to_route('admin.projects.index')->with(
            'success',
            'Projet mis à jour.',
        );
    }

    public function destroy(Project $project): RedirectResponse
    {
        ImageService::delete($project->image_url);

        $project->delete();

        return back()->with('success', 'Projet supprimé.');
    }

    public function syncPreview(GitHubService $github): View
    {
        $repos = $github->fetchPublicRepos();
        $existing = Project::whereNotNull('github_repo_id')
            ->pluck('github_repo_id')
            ->toArray();
        $candidates = [];

        foreach ($repos as $repo) {
            if (! $github->isStaticHtml($repo['full_name'])) {
                continue;
            }

            $candidates[] = [
                'id' => $repo['id'],
                'name' => $repo['name'],
                'full_name' => $repo['full_name'],
                'description' => $repo['description'] ?? '',
                'github_url' => $repo['html_url'],
                'default_branch' => $repo['default_branch'] ?? 'main',
                'is_imported' => in_array($repo['id'], $existing),
            ];
        }

        return view('admin.projects.sync', compact('candidates'));
    }

    public function confirmSync(
        Request $request,
    ): RedirectResponse {
        $selected = $request->input('repos', []);

        $selected = array_filter($selected, fn ($action) => ! empty($action['import']));

        if (empty($selected)) {
            return to_route('admin.projects.index')->with('error', 'Aucun projet sélectionné.');
        }

        $progress = JobProgress::create([
            'type' => 'github_sync',
            'reference_type' => Project::class,
            'reference_id' => 0,
            'total' => count($selected),
            'status' => 'processing',
        ]);

        $selected = array_filter($selected, fn ($a) => ! empty($a['import']));
        $total = count($selected);
        $progress->update(['total' => $total]);

        if ($total > 0) {
            $github = app(GitHubService::class);
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

            $fullNames = array_values(array_map(fn ($c) => $c['repo']['full_name'], $candidates));
            $repoIds = array_keys($candidates);

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
                    $progress->increment('completed');

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
                    $progress->increment('completed');
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
                    $progress->increment('completed');

                    continue;
                }

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
                    'live_url' => $this->githubPagesUrl($fullName),
                    'github_repo_id' => $repoId,
                ];

                if ($existing) {
                    $existing->update($data);
                } else {
                    Project::create($data);
                }

                $imported++;
                $progress->increment('completed');
            }

            $parts = [];
            if ($imported > 0) {
                $parts[] = "{$imported} synchronisé(s)";
            }
            if ($skipped > 0) {
                $parts[] = "{$skipped} ignoré(s)";
            }
            $message = $parts ? implode(', ', $parts).'.' : 'Aucun traitement.';

            $progress->update(['status' => 'completed', 'error' => $message]);
        } else {
            $progress->update(['status' => 'completed', 'error' => 'Aucun projet sélectionné.']);
        }

        return to_route('admin.projects.sync-github.progress', $progress);
    }

    public function syncProgress(JobProgress $progress): View
    {
        return view('admin.projects.sync-progress', compact('progress'));
    }

    public function syncStatus(JobProgress $progress): JsonResponse
    {
        return response()->json([
            'status' => $progress->status,
            'total' => $progress->total,
            'completed' => $progress->completed,
            'percentage' => $progress->percentage(),
            'error' => $progress->error,
        ]);
    }

    public function generateThumbnail(Project $project): RedirectResponse
    {
        abort_unless($project->type === 'web_static' && $project->live_url, 404);

        if ($project->thumbnail_status === 'processing') {
            return back()->with('error', 'Une miniature est déjà en cours de génération.');
        }

        $project->update(['thumbnail_status' => 'processing']);

        $screenshotUrl = 'https://mini.s-shot.ru/1280x1024/PNG/1024/?'.urlencode($project->live_url);

        $response = Http::timeout(30)->get($screenshotUrl);

        if ($response->failed()) {
            $project->update(['thumbnail_status' => 'failed']);

            return back()->with('error', 'Échec de la génération de la miniature.');
        }

        $imageUrl = ImageService::uploadContents($response->body(), 'projects');

        ImageService::delete($project->image_url);

        $project->update([
            'image_url' => $imageUrl,
            'thumbnail_status' => 'completed',
        ]);

        return back()->with('success', 'Miniature générée avec succès.');
    }

    public function cancelThumbnail(Project $project): RedirectResponse
    {
        abort_unless($project->type === 'web_static' && $project->live_url, 404);

        if (! in_array($project->thumbnail_status, ['pending', 'processing'])) {
            return back()->with('error', 'Aucune miniature en cours d\'annulation.');
        }

        $project->update(['thumbnail_status' => null]);

        return back()->with('success', 'Génération de la miniature annulée.');
    }

    public function thumbnailStatus(Project $project): JsonResponse
    {
        return response()->json([
            'status' => $project->thumbnail_status,
            'image_url' => $project->image_url,
        ]);
    }

    private function githubPagesUrl(string $fullName): string
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        if (str_ends_with($repo, '.github.io')) {
            return 'https://'.$repo.'/';
        }

        return 'https://'.$owner.'.github.io/'.$repo.'/';
    }
}
