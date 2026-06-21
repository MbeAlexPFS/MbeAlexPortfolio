@extends('layouts.app')

@section('title', isset($project) ? 'Modifier le projet' : 'Nouveau projet')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ isset($project) ? 'Modifier le projet' : 'Nouveau projet' }}</h1>

        <form method="POST" action="{{ isset($project) ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="mt-8 space-y-5">
            @csrf
            @if(isset($project)) @method('PUT') @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $project->title ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $project->type ?? '') === $type ? 'selected' : '' }}>
                            @switch($type)
                                @case('web_static') Site web statique @break
                                @case('web_dynamic') Site web dynamique @break
                                @case('web_live') Application web @break
                                @case('design') Design @break
                                @case('affiche') Affiche @break
                                @case('logo') Logo @break
                                @case('montage_video') Montage vidéo @break
                                @default {{ str_replace('_', ' ', ucfirst($type)) }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="6" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $project->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL</label>
                    <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $project->image_url ?? '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="github_url" class="block text-sm font-medium text-gray-700">GitHub URL</label>
                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $project->github_url ?? '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('github_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="live_url" class="block text-sm font-medium text-gray-700">Live URL</label>
                <input type="url" name="live_url" id="live_url" value="{{ old('live_url', $project->live_url ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('live_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Compétences</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                                {{ isset($project) && $project->skills->contains($skill->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $skill->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tags</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ isset($project) && $project->tags->contains($tag->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                {{ isset($project) ? 'Mettre à jour' : 'Créer le projet' }}
            </button>
        </form>
    </div>
@endsection
