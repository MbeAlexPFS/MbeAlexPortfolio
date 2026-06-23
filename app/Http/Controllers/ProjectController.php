<?php

namespace App\Http\Controllers;

use App\Models\JobProgress;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Tag;
use App\Services\GitHubService;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with(['skills', 'tags'])
            ->latest('created_at')
            ->paginate(12);

        $types = ['web_static', 'design', 'affiche', 'logo', 'montage_video'];

        return view('projects.index', compact('projects', 'types'));
    }

    public function show(Project $project): View
    {
        return view('projects.show', [
            'project' => $project->load(['skills', 'tags']),
        ]);
    }

    public function adminIndex(): View
    {
        $projects = Project::with(['skills', 'tags'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $skills = Skill::all();
        $tags = Tag::all();
        $types = ['web_static', 'design', 'affiche', 'logo', 'montage_video'];

        return view('admin.projects.form', compact('skills', 'tags', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => [
                'required',
                'in:web_static,design,affiche,logo,montage_video',
            ],
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
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url(
                $request->file('image')->store('projects', 'public'),
            );
        }

        $project = Project::create($data);

        if (! empty($data['skills'])) {
            $project->skills()->attach($data['skills']);
        }
        if (! empty($data['tags'])) {
            $project->tags()->attach($data['tags']);
        }

        return to_route('admin.projects.index')->with(
            'success',
            'Projet créé avec succès.',
        );
    }

    public function edit(Project $project): View
    {
        $project->load(['skills', 'tags']);
        $skills = Skill::all();
        $tags = Tag::all();
        $types = ['web_static', 'design', 'affiche', 'logo', 'montage_video'];

        return view(
            'admin.projects.form',
            compact('project', 'skills', 'tags', 'types'),
        );
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => [
                'required',
                'in:web_static,design,affiche,logo,montage_video',
            ],
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
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        if ($request->hasFile('image')) {
            if (
                $project->image_url &&
                str_starts_with($project->image_url, '/storage/projects/')
            ) {
                $oldPath = str_replace('/storage/', '', $project->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            $data['image_url'] = Storage::url(
                $request->file('image')->store('projects', 'public'),
            );
        }

        $project->update($data);

        $project->skills()->sync($data['skills'] ?? []);
        $project->tags()->sync($data['tags'] ?? []);

        return to_route('admin.projects.index')->with(
            'success',
            'Projet mis à jour.',
        );
    }

    public function destroy(Project $project): RedirectResponse
    {
        if (
            $project->image_url &&
            str_starts_with($project->image_url, '/storage/projects/')
        ) {
            $oldPath = str_replace('/storage/', '', $project->image_url);
            Storage::disk('public')->delete($oldPath);
        }

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

    public function preview(Project $project): View
    {
        abort_unless($project->live_url && $project->github_url, 404);

        return view('projects.preview', compact('project'));
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

        $filename = $project->id.'_thumbnail.png';

        Storage::disk('public')->put('projects/'.$filename, $response->body());

        if ($project->image_url && str_starts_with($project->image_url, '/storage/projects/')) {
            $oldPath = str_replace('/storage/', '', $project->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $project->update([
            'image_url' => Storage::url('projects/'.$filename),
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

    public function proxyContent(Project $project, Request $request): Response
    {
        abort_unless($project->live_url && $project->github_url, 404);

        $file = $request->query('file', 'index.html');
        $cacheKey = 'project_proxy_'.md5("{$project->id}_{$file}");

        $html = Cache::get($cacheKey);

        if ($html === null) {
            $fullName = trim(parse_url($project->github_url, PHP_URL_PATH) ?? '', '/');
            $branch = $this->extractBranch($project);

            $file = ltrim($file, './');

            $sourceUrl = "https://raw.githubusercontent.com/{$fullName}/{$branch}/{$file}";

            $response = Http::timeout(15)->get($sourceUrl);

            if ($response->failed()) {
                abort(502, 'Impossible de récupérer le contenu depuis GitHub.');
            }

            $html = $response->body();

            $rawBase = "https://raw.githubusercontent.com/{$fullName}/{$branch}/";
            $githubBlobBase = "https://github.com/{$fullName}/blob/{$branch}/";
            $htmlpreviewBase = "https://htmlpreview.github.io/?https://github.com/{$fullName}/blob/{$branch}/";

            $html = $this->inlineCssLinks($html, $rawBase, $githubBlobBase);
            $html = $this->inlineScripts($html, $rawBase);
            $html = $this->rewriteImages($html, $githubBlobBase);
            $html = $this->rewriteAnchors($html, $project, $htmlpreviewBase);
            $html = $this->rewriteCssUrlsInHtml($html, $githubBlobBase);

            Cache::put($cacheKey, $html, 300);
        }

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function inlineCssLinks(string $html, string $rawBase, string $githubBlobBase): string
    {
        return preg_replace_callback(
            '/<link\s[^>]*?\brel\s*=\s*["\']stylesheet["\'][^>]*?\bhref\s*=\s*["\']([^"\']+)["\'][^>]*>/i',
            function (array $m) use ($rawBase, $githubBlobBase) {
                $href = $m[1];

                if (preg_match('/^(https?:|data:|#|\/\/)/i', $href)) {
                    return $m[0];
                }

                if (str_contains($href, 'htmlpreview.github.io')) {
                    return $m[0];
                }

                $path = ltrim($href, './');
                $cssUrl = $rawBase.$path;

                $response = Http::timeout(10)->get($cssUrl);

                if ($response->failed()) {
                    return $m[0];
                }

                $css = $response->body();

                $cssDir = dirname($path);
                $cssBase = $cssDir !== '.' ? $githubBlobBase.$cssDir.'/' : $githubBlobBase;

                $css = preg_replace_callback(
                    '/url\(\s*["\']?([^"\'\)\s]+)["\']?\s*\)/i',
                    function (array $m) use ($cssBase) {
                        $url = $m[1];

                        if (preg_match('/^(https?:|data:|#|\/\/)/i', $url)) {
                            return $m[0];
                        }

                        return str_replace($url, $cssBase.$url.'?raw=true', $m[0]);
                    },
                    $css,
                );

                return '<style>'.$css.'</style>';
            },
            $html,
        );
    }

    private function inlineScripts(string $html, string $rawBase): string
    {
        return preg_replace_callback(
            '/<script\s[^>]*?\bsrc\s*=\s*["\']([^"\']+)["\'][^>]*><\/script>/i',
            function (array $m) use ($rawBase) {
                $src = $m[1];

                if (preg_match('/^(https?:|data:|#|\/\/)/i', $src)) {
                    return $m[0];
                }

                if (str_contains($src, 'htmlpreview.github.io')) {
                    return $m[0];
                }

                $path = ltrim($src, './');
                $jsUrl = $rawBase.$path;

                $response = Http::timeout(10)->get($jsUrl);

                if ($response->failed()) {
                    return $m[0];
                }

                $js = $response->body();

                return '<script>'.$js.'</script>';
            },
            $html,
        );
    }

    private function rewriteImages(string $html, string $githubBlobBase): string
    {
        return preg_replace_callback(
            '/<img\s[^>]*?\bsrc\s*=\s*["\']([^"\']+)["\'][^>]*>/i',
            function (array $m) use ($githubBlobBase) {
                $full = $m[0];
                $src = $m[1];

                if (preg_match('/^(https?:|data:|#|\/\/)/i', $src)) {
                    return $full;
                }

                if (str_contains($src, 'github.com') || str_contains($src, 'htmlpreview.github.io')) {
                    return $full;
                }

                $path = ltrim($src, './');
                $newSrc = $githubBlobBase.$path.'?raw=true';

                return str_replace('src="'.$src.'"', 'src="'.$newSrc.'"', $full);
            },
            $html,
        );
    }

    private function rewriteAnchors(string $html, Project $project, string $htmlpreviewBase): string
    {
        $previewRoute = route('projects.preview.proxy', $project);

        return preg_replace_callback(
            '/<a\s[^>]*?\bhref\s*=\s*["\']([^"\']+)["\'][^>]*>/i',
            function (array $m) use ($previewRoute) {
                $full = $m[0];
                $href = $m[1];

                if (preg_match('/^(https?:|mailto:|tel:|#|\/\/)/i', $href)) {
                    return $full;
                }

                if (str_contains($href, 'htmlpreview.github.io')) {
                    return $full;
                }

                if (preg_match('/\.(css|js|json|xml)$/i', $href)) {
                    return $full;
                }

                $path = ltrim($href, './');

                $newHref = $previewRoute.'?file='.urlencode($path);

                return str_replace('href="'.$href.'"', 'href="'.$newHref.'"', $full);
            },
            $html,
        );
    }

    private function rewriteCssUrlsInHtml(string $html, string $githubBlobBase): string
    {
        return preg_replace_callback(
            '/url\(\s*["\']?([^"\'\)\s]+)["\']?\s*\)/i',
            function (array $m) use ($githubBlobBase) {
                $url = $m[1];

                if (preg_match('/^(https?:|data:|#|\/\/)/i', $url)) {
                    return $m[0];
                }

                if (str_contains($url, 'github.com') || str_contains($url, 'htmlpreview.github.io')) {
                    return $m[0];
                }

                return str_replace($url, $githubBlobBase.$url.'?raw=true', $m[0]);
            },
            $html,
        );
    }

    private function extractBranch(Project $project): string
    {
        $innerUrl = str_replace('https://htmlpreview.github.io/?', '', $project->live_url);

        if (preg_match('#/(?:blob|raw)/([^/]+)/#', $innerUrl, $m)) {
            return $m[1];
        }

        if (preg_match('#^https?://raw\.githubusercontent\.com/[^/]+/[^/]+/([^/]+)/#', $innerUrl, $m)) {
            return $m[1];
        }

        return 'main';
    }
}
