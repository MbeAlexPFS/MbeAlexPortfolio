@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('blog.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour au blog</a>

        <article class="mt-8">
            @if($article->image_url)
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full rounded-xl mb-8">
            @endif

            <div class="flex items-center gap-3 text-sm text-gray-400 dark:text-dark-muted">
                <time datetime="{{ $article->created_at->toIso8601String() }}">{{ $article->created_at->format('d M Y') }}</time>
                @if($article->tags->isNotEmpty())
                    <span>&middot;</span>
                    @foreach($article->tags as $tag)
                        <span class="text-indigo-600 dark:text-indigo-400">{{ $tag->name }}</span>@if(!$loop->last), @endif
                    @endforeach
                @endif
            </div>

            <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text leading-tight">{{ $article->title }}</h1>

            <div class="mt-8 prose prose-gray dark:prose-invert max-w-none">
                {!! nl2br($article->content) !!}
            </div>
        </article>

        {{-- Comments --}}
        <section class="mt-16 pt-10 border-t border-gray-200 dark:border-dark-border">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-dark-text">Commentaires</h2>

            @auth
                @php
                    $userComment = $article->comments->where('user_id', Auth::id())->first();
                @endphp

                @if($userComment && $userComment->is_published)
                    <p class="mt-6 text-sm text-gray-500 dark:text-dark-muted">
                        <span class="text-green-600 dark:text-green-400 font-medium">&#10003;</span> Vous avez déjà commenté cet article.
                    </p>
                @elseif($userComment && !$userComment->is_published)
                    <p class="mt-6 text-sm text-amber-600 dark:text-amber-400">
                        Votre commentaire est en attente de validation.
                    </p>
                @else
                    <form method="POST" action="{{ route('comments.store', $article) }}" class="mt-6" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                        @csrf
                        <textarea name="content" rows="4" required
                            class="block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-card px-4 py-3 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            placeholder="Écrivez un commentaire...">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        <button type="submit" class="mt-3 bg-indigo-600 dark:bg-indigo-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                            Publier
                        </button>
                    </form>
                @endif
            @else
                <p class="mt-6 text-sm text-gray-500 dark:text-dark-muted">
                    <a href="{{ route('auth.login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Connectez-vous</a> pour laisser un commentaire.
                </p>
            @endauth

            <div class="mt-8 space-y-6">
                @forelse($article->comments->where('is_published', true) as $comment)
                    <div class="flex gap-4">
                        <x-avatar :user="$comment->user" size="md" />
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm text-gray-900 dark:text-dark-text">{{ $comment->user->pseudo }}</span>
                                <span class="text-xs text-gray-400 dark:text-dark-muted">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-dark-muted">{{ $comment->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-dark-muted">Aucun commentaire pour le moment.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
