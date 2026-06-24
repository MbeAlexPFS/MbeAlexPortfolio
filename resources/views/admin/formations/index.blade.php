@extends('layouts.app')

@section('title', 'Gérer les formations')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Formations</h1>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('admin.formations.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">+ Ajouter une formation</a>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($formations as $formation)
                <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-dark-text">{{ $formation->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-dark-muted mt-0.5">{{ $formation->institution }} &middot; {{ $formation->year }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs {{ $formation->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }} px-2 py-0.5 rounded-full font-medium">
                            {{ $formation->status === 'completed' ? 'Terminé' : 'En cours' }}
                        </span>
                        <a href="{{ route('admin.formations.edit', $formation) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Modifier</a>
                        <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" size="sm" loading-text="...">Supprimer</x-button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-10">Aucune formation.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $formations->links() }}</div>
    </div>
@endsection
