@extends('layouts.app')

@section('title', 'Gérer les sondages')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Sondages</h1>
            <a href="{{ route('admin.polls.create') }}" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Nouveau sondage</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($polls as $poll)
                <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="{{ $poll->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-dark-muted' }} px-2 py-0.5 rounded">
                                {{ $poll->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            <span class="text-gray-400 dark:text-dark-muted">{{ $poll->questions_count }} question(s)</span>
                            <span class="text-gray-400 dark:text-dark-muted">{{ $poll->views_count }} vue{{ $poll->views_count !== 1 ? 's' : '' }}</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-dark-text mt-1">{{ $poll->title }}</h3>
                        @if($poll->description)
                            <p class="text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($poll->description, 80) }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.polls.edit', $poll) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer</a>
                        <form method="POST" action="{{ route('admin.polls.destroy', $poll) }}">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" size="sm" loading-text="...">Supprimer</x-button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-10">Aucun sondage.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $polls->links() }}</div>
    </div>
@endsection
