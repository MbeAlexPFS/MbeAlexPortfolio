@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900">Blog</h1>
        <p class="mt-2 text-gray-500">Articles sur le développement, le design et plus encore.</p>

        @if($tags->isNotEmpty())
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full hover:bg-indigo-100 hover:text-indigo-700 transition">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 space-y-8">
            @forelse($articles as $article)
                <article class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition">
                    <div class="md:flex">
                        @if($article->image_url)
                            <div class="md:w-64 h-48 md:h-auto bg-gray-100 flex-shrink-0">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <time datetime="{{ $article->created_at->toIso8601String() }}">{{ $article->created_at->format('d M Y') }}</time>
                                    @if($article->tags->isNotEmpty())
                                        <span>&middot;</span>
                                        @foreach($article->tags as $tag)
                                            <span class="text-indigo-600">{{ $tag->name }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </div>
                                <h2 class="mt-2 text-xl font-semibold text-gray-900">
                                    <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-indigo-600 transition">{{ $article->title }}</a>
                                </h2>
                                @if($article->excerpt)
                                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $article->excerpt }}</p>
                                @endif
                            </div>
                            <a href="{{ route('blog.show', $article->slug) }}" class="mt-4 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                Lire la suite &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-center text-gray-400 py-20">Aucun article publié pour le moment.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $articles->links() }}
        </div>
    </div>
@endsection
