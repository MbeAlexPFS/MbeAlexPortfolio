@extends('layouts.app')

@section('title', 'Synchroniser GitHub')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Synchroniser GitHub</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Sélectionne les dépôts HTML statique à importer ou mettre à jour.</p>

        @if(empty($candidates))
            <p class="mt-10 text-center text-gray-400 dark:text-dark-muted py-10">Aucun dépôt HTML statique trouvé sur GitHub.</p>
        @else
            <form method="POST" action="{{ route('admin.projects.sync-github.confirm') }}" class="mt-8">
                @csrf

                <div class="space-y-3">
                    @foreach($candidates as $candidate)
                        <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                <input type="checkbox" name="repos[{{ $candidate['id'] }}][import]" value="1"
                                    id="repo_{{ $candidate['id'] }}" {{ $candidate['is_imported'] ? '' : 'checked' }}
                                    class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <label for="repo_{{ $candidate['id'] }}" class="font-medium text-gray-900 dark:text-dark-text cursor-pointer">{{ $candidate['name'] }}</label>
                                    <p class="text-xs text-gray-400 dark:text-dark-muted mt-0.5">branche : {{ $candidate['default_branch'] }}</p>
                                    @if($candidate['description'])
                                        <p class="text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($candidate['description'], 100) }}</p>
                                    @endif
                                    @if($candidate['is_imported'])
                                        <label class="mt-1 inline-flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400 cursor-pointer">
                                            <input type="checkbox" name="repos[{{ $candidate['id'] }}][replace]" value="1"
                                                class="rounded border-gray-300 dark:border-dark-border text-amber-500 focus:ring-amber-500">
                                            Remplacer l'existant
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ $candidate['github_url'] }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex-shrink-0 ml-4">
                                Voir sur GitHub &nearr;
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center gap-3">
                    <x-button type="submit" variant="primary" size="lg" loading-text="Synchronisation...">
                        Synchroniser la sélection
                    </x-button>
                    <p class="text-sm text-gray-400 dark:text-dark-muted">{{ count($candidates) }} dépôt(s) HTML statique trouvé(s)</p>
                </div>
            </form>
        @endif
    </div>
@endsection
