@extends('layouts.app')

@section('title', 'Gérer les projets')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Projets</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.projects.sync-github') }}" class="border border-gray-300 dark:border-dark-border text-gray-700 dark:text-dark-muted px-4 py-2 rounded-lg text-sm font-medium hover:border-gray-400 dark:hover:border-dark-muted transition">
                    Synchroniser GitHub
                </a>
                <a href="{{ route('admin.projects.create') }}" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Nouveau projet</a>
            </div>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($projects as $project)
                <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between"
                     x-data="{
                         thumbStatus: '{{ $project->thumbnail_status }}',
                         thumbPoll() {
                             if (this.thumbStatus !== 'pending' && this.thumbStatus !== 'processing') return;
                             setTimeout(() => {
                                 fetch('{{ route('admin.projects.thumbnail.status', $project) }}')
                                     .then(r => r.json())
                                     .then(d => { this.thumbStatus = d.status; if (d.status === 'completed' || d.status === 'failed') { location.reload(); } else { this.thumbPoll(); } });
                             }, 1500);
                         }
                     }"
                     x-init="thumbPoll()">
                    <div class="flex-1">
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                            @switch($project->type)
                                @case('web_static') Site web statique @break
                                @case('design') Design @break
                                @case('affiche') Affiche @break
                                @case('logo') Logo @break
                                @case('montage_video') Montage vidéo @break
                                @default {{ str_replace('_', ' ', $project->type) }}
                            @endswitch
                        </span>
                        <h3 class="font-semibold text-gray-900 dark:text-dark-text">{{ $project->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($project->description, 100) }}</p>
                    </div>
                    <div class="flex gap-2 ml-4 items-center">
                        @if($project->type === 'web_static' && $project->live_url && $project->github_url)
                            <a href="{{ route('projects.preview', $project) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">Aperçu</a>
                            <template x-if="thumbStatus === 'pending' || thumbStatus === 'processing'">
                                <span class="text-sm text-amber-500 animate-pulse flex items-center gap-2">
                                    Miniature...
                                    <form method="POST" action="{{ route('admin.projects.thumbnail.cancel', $project) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-500 hover:text-red-600 underline">Annuler</button>
                                    </form>
                                </span>
                            </template>
                            <template x-if="!thumbStatus || thumbStatus === 'failed'">
                                <form method="POST" action="{{ route('admin.projects.thumbnail', $project) }}"
                                      class="inline" x-data="{ submitting: false }"
                                      x-on:submit="submitting = true">
                                    @csrf
                                    <button type="submit" :disabled="submitting"
                                            :class="submitting && 'opacity-50 cursor-not-allowed'"
                                            class="text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium">
                                        <span x-show="!submitting">Miniature</span>
                                        <span x-show="submitting">Génération...</span>
                                    </button>
                                </form>
                            </template>
                            <template x-if="thumbStatus === 'completed'">
                                <span class="text-sm text-green-600 dark:text-green-400 font-medium">Miniature &#10003;</span>
                            </template>
                            <template x-if="thumbStatus === 'completed'">
                                <form method="POST" action="{{ route('admin.projects.thumbnail', $project) }}"
                                      class="inline" x-data="{ submitting: false }"
                                      x-on:submit="if(confirm('Remplacer la miniature existante ?')) submitting = true; else $event.preventDefault()">
                                    @csrf
                                    <button type="submit" :disabled="submitting"
                                            :class="submitting && 'opacity-50 cursor-not-allowed'"
                                            class="text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium">
                                        <span x-show="!submitting">Remplacer</span>
                                        <span x-show="submitting">Génération...</span>
                                    </button>
                                </form>
                            </template>
                        @endif
                        <a href="{{ route('admin.projects.edit', $project) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Modifier</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                              class="inline" x-data="{ submitting: false }"
                              x-on:submit="if(confirm('Supprimer ce projet ?')) submitting = true; else $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" :disabled="submitting"
                                    :class="submitting && 'opacity-50 cursor-not-allowed'"
                                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-10">Aucun projet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $projects->links() }}</div>
    </div>
@endsection
