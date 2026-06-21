@extends('layouts.app')

@section('title', 'Gérer les projets')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900">Projets</h1>
            <a href="{{ route('admin.projects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Nouveau projet</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($projects as $project)
                <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-center justify-between">
                    <div class="flex-1">
                        <span class="text-xs text-indigo-600 uppercase tracking-wider">
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
                        <h3 class="font-semibold text-gray-900">{{ $project->title }}</h3>
                        <p class="text-sm text-gray-500">{{ Str::limit($project->description, 100) }}</p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Modifier</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Supprimer ce projet ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10">Aucun projet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $projects->links() }}</div>
    </div>
@endsection
