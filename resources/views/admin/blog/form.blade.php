@extends('layouts.app')

@section('title', isset($article) ? "Modifier l'article" : 'Nouvel article')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ isset($article) ? "Modifier l'article" : 'Nouvel article' }}</h1>

        <form method="POST" action="{{ isset($article) ? route('admin.blog.update', $article) : route('admin.blog.store') }}" class="mt-8 space-y-5" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
            @csrf
            @if(isset($article)) @method('PUT') @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Extrait</label>
                <textarea name="excerpt" id="excerpt" rows="2"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                @error('excerpt') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Contenu</label>
                <textarea name="content" id="content" rows="15" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('content', $article->content ?? '') }}</textarea>
                @error('content') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Image URL</label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $article->image_url ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('image_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" value="1" id="is_published"
                    {{ old('is_published', $article->is_published ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                <label for="is_published" class="text-sm text-gray-700 dark:text-dark-text">Publier</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Tags</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-dark-muted">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ isset($article) && $article->tags->contains($tag->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                {{ isset($article) ? 'Mettre à jour' : "Créer l'article" }}
            </button>
        </form>
    </div>
@endsection
