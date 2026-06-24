@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-dark-text text-center">Inscription</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-dark-muted text-center">Créez un compte pour participer aux sondages et commenter.</p>

        <form method="POST" action="{{ route('auth.register') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="pseudo" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Pseudo</label>
                <input type="text" name="pseudo" id="pseudo" value="{{ old('pseudo') }}" required autofocus
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('pseudo') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Mot de passe</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('password_confirmation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <x-button type="submit" variant="primary" size="lg" class="w-full" loading-text="Inscription...">
                Créer mon compte
            </x-button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-dark-muted">
            Déjà un compte ? <a href="{{ route('auth.login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Se connecter</a>
        </p>
    </div>
@endsection
