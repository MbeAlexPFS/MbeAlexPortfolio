<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('user', 'tags')
            ->where('is_published', true)
            ->latest()
            ->paginate(9);

        $tags = Tag::has('articles')->get();

        return view('blog.index', compact('articles', 'tags'));
    }

    public function show(string $slug): View
    {
        $article = Article::with('user', 'tags', 'comments.user')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('blog.show', compact('article'));
    }

    public function adminIndex(): View
    {
        $articles = Article::with('user', 'tags')
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
}
