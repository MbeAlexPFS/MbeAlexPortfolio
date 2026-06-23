@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Mon profil</h1>

        {{-- Avatar --}}
        <section class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Photo de profil</h2>
            <div class="mt-4 flex items-center gap-6">
                <x-avatar :user="$user" size="xl" />
                <div class="space-y-3">
                    <form method="POST" action="{{ route('profile.update-avatar') }}" enctype="multipart/form-data" class="flex items-center gap-3" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                        @csrf
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp" required
                               class="block w-full text-sm text-gray-500 dark:text-dark-muted file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 transition">
                        <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition flex-shrink-0">Upload</button>
                    </form>
                    @if($user->avatar_url)
                        <form method="POST" action="{{ route('profile.delete-avatar') }}" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium">Supprimer la photo</button>
                        </form>
                    @endif
                    @error('avatar') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Pseudo --}}
        <section class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Pseudo</h2>
            <form method="POST" action="{{ route('profile.update-pseudo') }}" class="mt-4 flex gap-3" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                @csrf
                @method('PUT')
                <input type="text" name="pseudo" value="{{ $user->pseudo }}" required
                    class="flex-1 rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Modifier</button>
            </form>
        </section>

        {{-- Password --}}
        <section class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Mot de passe</h2>
            <form method="POST" action="{{ route('profile.update-password') }}" class="mt-4 space-y-4" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                @csrf
                @method('PUT')
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Mot de passe actuel</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Nouveau mot de passe</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Confirmer</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2 text-sm text-gray-900 dark:text-dark-text focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
                <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Changer le mot de passe</button>
            </form>
        </section>

        {{-- Newsletters --}}
        <section class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Newsletters</h2>
            <form method="POST" action="{{ route('profile.update-newsletters') }}" class="mt-4 space-y-3" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
                @csrf
                @method('PUT')
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="newsletter_articles" value="1" {{ $user->newsletter_articles ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-dark-muted">Recevoir les nouveaux articles</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="newsletter_polls" value="1" {{ $user->newsletter_polls ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-dark-muted">Recevoir les nouveaux sondages</span>
                </label>
                <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Enregistrer</button>
            </form>
        </section>

        {{-- Poll history --}}
        <section class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Historique des participations</h2>
            @if($answers->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach($answers->groupBy(fn($a) => $a->question->poll->title) as $pollTitle => $pollAnswers)
                        <div class="text-sm">
                            <h3 class="font-medium text-gray-700 dark:text-dark-muted">{{ $pollTitle }}</h3>
                            <p class="text-gray-400 dark:text-dark-muted text-xs mt-1">{{ $pollAnswers->count() }} réponse(s) — {{ $pollAnswers->first()->created_at->format('d/m/Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-gray-400 dark:text-dark-muted">Vous n'avez participé à aucun sondage.</p>
            @endif
        </section>
    </div>
@endsection
