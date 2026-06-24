<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('user', 'tags')
            ->withCount('views', 'comments')
            ->where('is_published', true)
            ->latest()
            ->paginate(9);

        $tags = Tag::has('articles')->get();

        return view('blog.index', compact('articles', 'tags'));
    }

    public function show(string $slug): View
    {
        $article = Article::with('user', 'tags', 'comments.user')
            ->withCount('views', 'comments')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $article->recordView();

        return view('blog.show', compact('article'));
    }

    public function adminIndex(): View
    {
        $articles = Article::with('user', 'tags')
            ->withCount('views', 'comments')
            ->latest()
            ->paginate(20);

        return view('admin.blog.index', compact('articles'));
    }

    public function create(): View
    {
        $tags = Tag::all();

        return view('admin.blog.form', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_published' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $data['user_id'] = Auth::id();
        $data['is_published'] = $request->boolean('is_published');

        $article = Article::create($data);

        if (! empty($data['tags'])) {
            $article->tags()->attach($data['tags']);
        }

        return to_route('admin.blog.index')->with('success', 'Article créé.');
    }

    public function edit(Article $article): View
    {
        $article->load('tags');
        $tags = Tag::all();

        return view('admin.blog.form', compact('article', 'tags'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_published' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $wasPublished = $article->is_published;
        $data['is_published'] = $request->boolean('is_published');

        $article->update($data);
        $article->tags()->sync($data['tags'] ?? []);

        return to_route('admin.blog.index')->with('success', 'Article mis à jour.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return back()->with('success', 'Article supprimé.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ]);

        $path = $request->file('image')->store('articles', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'filename' => basename($path),
        ]);
    }

    public function previewMarkdown(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $html = Str::of($data['content'])->markdown([
            'html_input' => 'allow',
        ]);

        return response()->json(['html' => (string) $html]);
    }
}
