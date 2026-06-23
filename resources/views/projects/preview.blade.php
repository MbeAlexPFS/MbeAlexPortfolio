@extends('layouts.app')

@section('title', $project->title . ' — Aperçu')

@section('content')
    <div class="h-dvh flex flex-col overflow-hidden"
         x-data="{ loading: true, timedOut: false, fullscreen: false }"
         x-init="setTimeout(() => { if (loading) timedOut = true; }, 15000); document.addEventListener('fullscreenchange', () => { fullscreen = !!document.fullscreenElement; })">
        <header class="bg-white dark:bg-dark-card border-b border-gray-200 dark:border-dark-border px-4 sm:px-6 py-3 flex items-center justify-between shrink-0 gap-3">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition shrink-0">&larr; Retour</a>
                <span class="text-sm font-medium text-gray-700 dark:text-dark-text truncate">{{ $project->title }}</span>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button @click="if (!document.fullscreenElement) { $refs.iframe.requestFullscreen(); fullscreen = true; } else { document.exitFullscreen(); fullscreen = false; }"
                        class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                        x-text="fullscreen ? 'Quitter' : 'Plein écran'">
                </button>
                <a href="{{ $project->github_url }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    GitHub &nearr;
                </a>
            </div>
        </header>

        <div class="bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/40 px-4 sm:px-6 py-2 text-xs text-amber-700 dark:text-amber-300 shrink-0">
            Le rendu intégré peut présenter des limitations. Pour voir le projet dans des conditions optimales,
            <a href="{{ route('contact.show') }}" class="underline font-medium hover:text-amber-800 dark:hover:text-amber-200">contactez-moi</a>,
            je ferai un test spécial pour vous.
        </div>

        <div class="relative flex-1 min-h-0">
            <div x-show="loading && !timedOut" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-dark-card text-gray-400 dark:text-dark-muted z-10">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-500 border-t-transparent mx-auto"></div>
                    <p class="mt-3 text-sm">Chargement du rendu...</p>
                </div>
            </div>

            <div x-show="timedOut" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-dark-card text-gray-400 dark:text-dark-muted z-10">
                <div class="text-center">
                    <p class="text-gray-500 dark:text-dark-muted">Le rendu prend plus de temps que prévu.</p>
                    <button @click="timedOut = false; $refs.iframe.src = '{{ route('projects.preview.proxy', $project) }}'; loading = true"
                            class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Réessayer
                    </button>
                </div>
            </div>

            <iframe
                x-ref="iframe"
                src="{{ route('projects.preview.proxy', $project) }}"
                class="absolute inset-0 w-full h-full border-0"
                title="Aperçu de {{ $project->title }}"
                sandbox="allow-scripts allow-same-origin allow-popups allow-fullscreen"
                x-on:load="loading = false; timedOut = false"
            ></iframe>
        </div>
    </div>
@endsection
