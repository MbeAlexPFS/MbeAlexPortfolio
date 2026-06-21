@extends('layouts.app')

@section('title', $project->title)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('projects.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour aux projets</a>

        <div class="mt-6 aspect-video bg-gray-100 dark:bg-dark-bg rounded-xl overflow-hidden flex items-center justify-center text-gray-300 dark:text-dark-muted">
            @if($project->image_url)
                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            @else
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            @endif
        </div>

        <div class="mt-8">
            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                @switch($project->type)
                    @case('web_static') Site web statique @break
                    @case('web_dynamic') Site web dynamique @break
                    @case('web_live') Application web @break
                    @case('design') Design @break
                    @case('affiche') Affiche @break
                    @case('logo') Logo @break
                    @case('montage_video') Montage vidéo @break
                    @default {{ str_replace('_', ' ', $project->type) }}
                @endswitch
            </span>
            <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ $project->title }}</h1>

            <div class="mt-6 prose prose-gray dark:prose-invert max-w-none">
                {{ nl2br(e($project->description)) }}
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @if($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" class="inline-flex items-center gap-2 border border-gray-300 dark:border-dark-border text-gray-700 dark:text-dark-muted px-4 py-2 rounded-lg text-sm hover:border-gray-400 dark:hover:border-dark-muted transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                        Code source
                    </a>
                @endif
                @if($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Voir le projet
                    </a>
                @endif
            </div>

            @if($project->skills->isNotEmpty())
                <div class="mt-10">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Technologies utilisées</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($project->skills as $skill)
                            <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm px-3 py-1 rounded-full">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($project->tags->isNotEmpty())
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Tags</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($project->tags as $tag)
                            <span class="bg-gray-100 dark:bg-dark-border text-gray-600 dark:text-dark-muted text-sm px-3 py-1 rounded-full">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
