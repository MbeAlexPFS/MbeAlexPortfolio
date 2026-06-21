@extends('layouts.app')

@section('title', 'Gérer le blog')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900">Articles</h1>
            <a href="{{ route('admin.blog.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Nouvel article</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($articles as $article)
                <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span>{{ $article->created_at->format('d/m/Y') }}</span>
                            <span class="px-1.5 py-0.5 rounded {{ $article->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $article->is_published ? 'Publié' : 'Brouillon' }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mt-1">{{ $article->title }}</h3>
                        <p class="text-sm text-gray-500">{{ Str::limit($article->excerpt ?? $article->content, 80) }}</p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.blog.edit', $article) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Modifier</a>
                        <form method="POST" action="{{ route('admin.blog.destroy', $article) }}" onsubmit="return confirm('Supprimer cet article ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10">Aucun article.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $articles->links() }}</div>
    </div>
@endsection
