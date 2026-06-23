@extends('layouts.app')

@section('title', isset($project) ? 'Modifier le projet' : 'Nouveau projet')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ isset($project) ? 'Modifier le projet' : 'Nouveau projet' }}</h1>

        <form method="POST" action="{{ isset($project) ? route('admin.projects.update', $project) : route('admin.projects.store') }}" enctype="multipart/form-data" class="mt-8 space-y-5" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
            @csrf
            @if(isset($project)) @method('PUT') @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $project->title ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Type</label>
                <select name="type" id="type" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $project->type ?? '') === $type ? 'selected' : '' }}>
                            @switch($type)
                                @case('web_static') Site web statique @break
                                @case('design') Design @break
                                @case('affiche') Affiche @break
                                @case('logo') Logo @break
                                @case('montage_video') Montage vidéo @break
                                @default {{ str_replace('_', ' ', ucfirst($type)) }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Description</label>
                <textarea name="description" id="description" rows="6" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $project->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Image / Vidéo</label>
                <div class="mt-1 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="image" class="block text-xs text-gray-500 dark:text-dark-muted mb-1">Uploader un fichier</label>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime"
                            class="block w-full text-sm text-gray-500 dark:text-dark-muted file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 transition">
                        @error('image') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        @if(isset($project) && $project->image_url && str_starts_with($project->image_url, '/storage/'))
                            <div class="mt-2">
                                @if(preg_match('/\.(mp4|webm|mov|ogg)$/i', $project->image_url))
                                    <video src="{{ $project->image_url }}" class="w-24 h-16 rounded object-cover" muted></video>
                                @else
                                    <img src="{{ $project->image_url }}" alt="" class="w-24 h-16 rounded object-cover">
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <label for="image_url" class="block text-xs text-gray-500 dark:text-dark-muted mb-1">Ou URL externe</label>
                        <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $project->image_url ?? '') }}"
                            class="block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="https://...">
                        @error('image_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="github_url" class="block text-sm font-medium text-gray-700 dark:text-dark-text">GitHub URL</label>
                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $project->github_url ?? '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('github_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="live_url" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Live URL</label>
                    <input type="url" name="live_url" id="live_url" value="{{ old('live_url', $project->live_url ?? '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('live_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Compétences</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-dark-muted">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                                {{ isset($project) && $project->skills->contains($skill->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                            {{ $skill->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Tags</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-dark-muted">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ isset($project) && $project->tags->contains($tag->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3"
                 @if(isset($project))
                 x-data="{
                     thumbStatus: '{{ $project->thumbnail_status }}',
                     thumbPoll() {
                         if (this.thumbStatus !== 'pending' && this.thumbStatus !== 'processing') return;
                         setTimeout(() => {
                             fetch('{{ route('admin.projects.thumbnail.status', $project) }}')
                                 .then(r => r.json())
                                 .then(d => { this.thumbStatus = d.status; if (d.status === 'completed') { location.reload(); } else if (d.status !== 'failed') { this.thumbPoll(); } });
                         }, 1500);
                     }
                 }"
                 x-init="thumbPoll()"
                 @endif>
                <button type="submit" class="bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                    {{ isset($project) ? 'Mettre à jour' : 'Créer le projet' }}
                </button>
                @if(isset($project) && $project->type === 'web_static' && $project->live_url)
                    <template x-if="thumbStatus === 'pending' || thumbStatus === 'processing'">
                        <span class="text-sm text-amber-500 animate-pulse flex items-center gap-2">
                            Génération de la miniature...
                            <form method="POST" action="{{ route('admin.projects.thumbnail.cancel', $project) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-600 underline">Annuler</button>
                            </form>
                        </span>
                    </template>
                    <template x-if="!thumbStatus || thumbStatus === 'failed' || thumbStatus === ''">
                        <form method="POST" action="{{ route('admin.projects.thumbnail', $project) }}"
                              @submit="$event.target.querySelector('button').disabled = true; $event.target.querySelector('button').textContent = '...'">
                            @csrf
                            <button type="submit" class="border border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                                Générer la miniature
                            </button>
                        </form>
                    </template>
                    <template x-if="thumbStatus === 'completed'">
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">Miniature prête &#10003;</span>
                    </template>
                    <template x-if="thumbStatus === 'completed'">
                        <form method="POST" action="{{ route('admin.projects.thumbnail', $project) }}"
                              class="inline"
                              x-on:submit="if(confirm('Remplacer la miniature existante ?')) $el.querySelector('button[type=submit]').disabled = true; else $event.preventDefault()">
                            @csrf
                            <button type="submit" class="border border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">Remplacer</button>
                        </form>
                    </template>
                @endif
            </div>
        </form>
    </div>
@endsection
