@extends('layouts.app')

@section('title', 'Sondages')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Sondages</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Participez aux sondages et donnez votre avis.</p>

        <div class="mt-10 space-y-4">
            @forelse($polls as $poll)
                <a href="{{ route('polls.show', $poll) }}" class="block bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6 hover:shadow-md dark:hover:shadow-2xl transition scroll-reveal">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">{{ $poll->title }}</h2>
                    @if($poll->description)
                        <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($poll->description, 150) }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-4 text-xs text-gray-400 dark:text-dark-muted">
                        <span>{{ $poll->questions_count }} question(s)</span>
                        @if($poll->end_date)
                            <span>Fin : {{ $poll->end_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-20">Aucun sondage actif pour le moment.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $polls->links() }}
        </div>
    </div>
@endsection
