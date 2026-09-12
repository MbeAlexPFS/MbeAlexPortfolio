@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-dark-text text-center">Connexion</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-dark-muted text-center">Espace réservé à l'administrateur du site.</p>

        <form method="POST" action="{{ route('auth.login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Mot de passe</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-600 dark:text-dark-muted">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                    Se souvenir de moi
                </label>
                <a href="{{ route('home') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">Retour au site</a>
            </div>
            <x-button type="submit" variant="primary" size="lg" class="w-full" loading-text="Connexion...">
                Se connecter
            </x-button>
        </form>
    </div>
@endsection