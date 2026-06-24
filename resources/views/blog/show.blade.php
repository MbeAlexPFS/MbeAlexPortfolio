@extends('layouts.app')

@section('title', $article->title)

@php
    $htmlWithIds = $article->content_html;
    $tocItems = [];

    if ($htmlWithIds) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<div>' . mb_encode_numericentity($htmlWithIds, [0x80, 0x10FFFF, 0, ~0], 'UTF-8') . '</div>', LIBXML_HTML_NOIMPLICIT | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $headings = $dom->getElementsByTagName('*');
        $toReplace = [];

        foreach ($headings as $node) {
            if (!in_array($node->tagName, ['h1', 'h2', 'h3'], true)) continue;

            $text = trim($node->textContent);
            $anchor = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower(trim($text))));
            $anchor = trim($anchor, '-');
            if (empty($anchor)) $anchor = 'heading-' . uniqid();

            $existing = $node->getAttribute('id');
            if ($existing) $anchor = $existing;

            $node->setAttribute('id', $anchor);
            $tocItems[] = ['tag' => $node->tagName, 'text' => $text, 'anchor' => $anchor];
        }

        foreach ($headings as $node) {
            if (in_array($node->tagName, ['h1', 'h2', 'h3'], true)) {
                $inner = '';
                foreach ($node->childNodes as $child) {
                    $inner .= $dom->saveHTML($child);
                }
                $toReplace[] = [$node, '<' . $node->tagName . ' id="' . $node->getAttribute('id') . '">' . $inner . '</' . $node->tagName . '>'];
            }
        }

        foreach (array_reverse($toReplace) as [$oldNode, $newHtml]) {
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($newHtml);
            $oldNode->parentNode->replaceChild($fragment, $oldNode);
        }

        $body = $dom->getElementsByTagName('div')->item(0);
        $htmlWithIds = '';
        if ($body) {
            foreach ($body->childNodes as $child) {
                $htmlWithIds .= $dom->saveHTML($child);
            }
        }
    }
@endphp

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('blog.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour au blog</a>

        <div class="mt-8 lg:grid lg:grid-cols-[minmax(0,1fr)_240px] lg:gap-10">
            <article>
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
                    <span>&middot;</span>
                    <span>{{ $article->views_count }} vue{{ $article->views_count !== 1 ? 's' : '' }}</span>
                </div>

                <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text leading-tight">{{ $article->title }}</h1>

                @if($article->excerpt)
                    <p class="mt-3 text-lg text-gray-500 dark:text-dark-muted leading-relaxed">{{ $article->excerpt }}</p>
                @endif

                @if(!empty($tocItems))
                    <nav class="mt-8 p-4 bg-gray-50 dark:bg-dark-border/50 rounded-xl border border-gray-200 dark:border-dark-border lg:hidden">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-dark-text uppercase tracking-wider">Sommaire</h2>
                        <ul class="mt-2 space-y-1 text-sm">
                            @foreach($tocItems as $item)
                                <li class="{{ $item['tag'] === 'h2' ? 'ml-4' : ($item['tag'] === 'h3' ? 'ml-8' : '') }}">
                                    <a href="#{{ $item['anchor'] }}" class="text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">{{ $item['text'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif

                <div class="mt-8 prose prose-gray dark:prose-invert max-w-none">
                    {!! $htmlWithIds !!}
                </div>

                <div class="mt-6 flex items-center gap-3 text-xs text-gray-400 dark:text-dark-muted">
                    <span>{{ $article->views_count }} vue{{ $article->views_count !== 1 ? 's' : '' }}</span>
                    <span>{{ $article->comments_count }} commentaire{{ $article->comments_count !== 1 ? 's' : '' }}</span>
                </div>
            </article>

            @if(!empty($tocItems))
                <aside class="hidden lg:block">
                    <div class="sticky top-24">
                        <h2 class="text-xs font-semibold text-gray-400 dark:text-dark-muted uppercase tracking-wider">Sommaire</h2>
                        <ul class="mt-3 space-y-2 text-sm border-l-2 border-gray-200 dark:border-dark-border">
                            @foreach($tocItems as $item)
                                <li>
                                    <a href="#{{ $item['anchor'] }}" class="block {{ $item['tag'] === 'h2' ? 'ml-4' : ($item['tag'] === 'h3' ? 'ml-8' : '') }} text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition no-underline">{{ $item['text'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            @endif
        </div>

        {{-- Comments --}}
        <section class="mt-16 pt-10 border-t border-gray-200 dark:border-dark-border max-w-3xl">
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-dark-text">Commentaires</h2>
                <span class="text-sm text-gray-400 dark:text-dark-muted">({{ $article->comments_count }})</span>
            </div>

            @auth
                @php
                    $userComment = $article->comments->where('user_id', Auth::id())->first();
@endsection

                @if($userComment && $userComment->is_published)
                    <p class="mt-6 text-sm text-gray-500 dark:text-dark-muted">
                        <span class="text-green-600 dark:text-green-400 font-medium">&#10003;</span> Vous avez déjà commenté cet article.
                    </p>
                @elseif($userComment && !$userComment->is_published)
                    <p class="mt-6 text-sm text-amber-600 dark:text-amber-400">
                        Votre commentaire est en attente de validation.
                    </p>
                @else
                    <form method="POST" action="{{ route('comments.store', $article) }}" class="mt-6">
                        @csrf
                        <textarea name="content" rows="4" required
                            class="block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-card px-4 py-3 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            placeholder="Écrivez un commentaire...">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        <x-button type="submit" variant="primary" size="md" class="mt-3" loading-text="Publication...">
                            Publier
                        </x-button>
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
