@extends('layouts.app')

@section('title', 'Projets')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Mes projets</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Découvrez l'ensemble de mes réalisations.</p>

        <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <a href="{{ route('projects.show', $project) }}" class="group bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden hover:shadow-lg dark:hover:shadow-2xl transition scroll-reveal">
                    <div class="aspect-video bg-gray-100 dark:bg-dark-bg flex items-center justify-center text-gray-300 dark:text-dark-muted">
                        @if($project->image_url)
                            <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
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
                        <h3 class="mt-1 font-semibold text-gray-900 dark:text-dark-text group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $project->title }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-dark-muted line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                        @if($project->tags->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach($project->tags as $tag)
                                    <span class="text-xs bg-gray-100 dark:bg-dark-border text-gray-600 dark:text-dark-muted px-2 py-0.5 rounded">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-gray-400 dark:text-dark-muted py-20">Aucun projet pour le moment.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $projects->links() }}
        </div>
    </div>
@endsection
