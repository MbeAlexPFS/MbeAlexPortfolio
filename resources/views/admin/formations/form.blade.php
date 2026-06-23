@extends('layouts.app')

@section('title', isset($formation) ? "Modifier la formation" : 'Nouvelle formation')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.formations.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ isset($formation) ? 'Modifier la formation' : 'Nouvelle formation' }}</h1>

        <form method="POST" action="{{ isset($formation) ? route('admin.formations.update', $formation) : route('admin.formations.store') }}" class="mt-8 space-y-5" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
            @csrf
            @if(isset($formation)) @method('PUT') @endif

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Nom de la formation</label>
                <input type="text" name="name" id="name" value="{{ old('name', $formation->name ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="institution" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Centre / Service de formation</label>
                <input type="text" name="institution" id="institution" value="{{ old('institution', $formation->institution ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('institution') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Année d'acquisition</label>
                <input type="text" name="year" id="year" value="{{ old('year', $formation->year ?? '') }}" placeholder="2025" required maxlength="4"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('year') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Statut</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="completed" {{ old('status', $formation->status ?? '') === 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="in_progress" {{ old('status', $formation->status ?? '') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                {{ isset($formation) ? 'Mettre à jour' : 'Ajouter la formation' }}
            </button>
        </form>
    </div>
@endsection
