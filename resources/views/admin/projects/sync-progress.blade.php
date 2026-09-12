@extends('layouts.app')

@section('title', 'Synchronisation en cours')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
         x-data="{
             status: '{{ $progress->status }}',
             total: {{ $progress->total }},
             completed: {{ $progress->completed }},
             percentage: {{ $progress->percentage() }},
             message: '',
             poll() {
                 if (this.status === 'completed' || this.status === 'failed') return;
                 setTimeout(() => {
                     fetch('{{ route('admin.projects.sync-github.status', $progress) }}')
                         .then(r => r.json())
                         .then(d => {
                             this.status = d.status;
                             this.total = d.total;
                             this.completed = d.completed;
                             this.percentage = d.percentage;
                             this.message = d.error || '';
                             if (this.status === 'completed' || this.status === 'failed') {
                                 if (this.status === 'completed') {
                                     setTimeout(() => window.location.href = '{{ route('admin.projects.index') }}', 1500);
                                 }
                             } else {
                                 this.poll();
                             }
                         });
                 }, 1000);
             },
             init() { this.poll(); }
         }">
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Projets</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Synchronisation GitHub</h1>

        <div class="mt-10 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-8 text-center">
            <template x-if="status === 'pending' || status === 'processing'">
                <div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-dark-muted" x-text="'Synchronisation en cours... (' + completed + '/' + total + ')'"></p>
                    <div class="mt-4 max-w-md mx-auto bg-gray-200 dark:bg-dark-border rounded-full h-3 overflow-hidden">
                        <div class="h-full bg-indigo-600 dark:bg-indigo-500 rounded-full transition-all duration-500" :style="'width: ' + percentage + '%'"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-400 dark:text-dark-muted" x-text="percentage + '%'"></p>
                </div>
            </template>

            <template x-if="status === 'completed'">
                <div>
                    <div class="text-green-500 text-5xl">&#10003;</div>
                    <p class="mt-4 text-gray-700 dark:text-dark-text font-medium">Synchronisation terminée</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted" x-text="message || 'Importé(s) avec succès.'"></p>
                    <p class="mt-2 text-xs text-gray-400 dark:text-dark-muted">Redirection...</p>
                </div>
            </template>

            <template x-if="status === 'failed'">
                <div>
                    <div class="text-red-500 text-5xl">&#10007;</div>
                    <p class="mt-4 text-gray-700 dark:text-dark-text font-medium">Échec de la synchronisation</p>
                    <p class="mt-1 text-sm text-red-500" x-text="message || 'Une erreur est survenue.'"></p>
                    <a href="{{ route('admin.projects.index') }}" class="mt-4 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Retour aux projets</a>
                </div>
            </template>
        </div>
    </div>
@endsection
