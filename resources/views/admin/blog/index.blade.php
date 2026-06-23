@extends('layouts.app')

@section('title', 'Gérer le blog')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Articles</h1>
            <a href="{{ route('admin.blog.create') }}" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Nouvel article</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($articles as $article)
                <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-dark-muted">
                            <span>{{ $article->created_at->format('d/m/Y') }}</span>
                            <span class="px-1.5 py-0.5 rounded {{ $article->is_published ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-dark-muted' }}">
                                {{ $article->is_published ? 'Publié' : 'Brouillon' }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-dark-text mt-1">{{ $article->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($article->excerpt ?? $article->content, 80) }}</p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.blog.edit', $article) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Modifier</a>
                        <form method="POST" action="{{ route('admin.blog.destroy', $article) }}" onsubmit="return confirm('Supprimer cet article ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-10">Aucun article.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $articles->links() }}</div>
    </div>
@endsection
