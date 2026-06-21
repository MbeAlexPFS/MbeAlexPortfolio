@extends('layouts.app')

@section('title', 'Gérer les compétences')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Administration</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Compétences</h1>

        <form method="POST" action="{{ route('admin.skills.store') }}" class="mt-8 bg-white border border-gray-200 rounded-xl p-6 grid sm:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" id="name" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700">Niveau (1-5)</label>
                <input type="number" name="level" id="level" min="1" max="5" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <input type="text" name="category" id="category" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Ajouter</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            @forelse($skills as $skill)
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $skill->category }}</span>
                        <span class="font-medium text-gray-900">{{ $skill->name }}</span>
                        <span class="text-sm text-gray-400">{{ $skill->level }}/5</span>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.skills.update', $skill) }}" class="flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $skill->name }}" required class="w-24 rounded border border-gray-300 px-2 py-1 text-xs">
                            <input type="number" name="level" value="{{ $skill->level }}" min="1" max="5" required class="w-14 rounded border border-gray-300 px-2 py-1 text-xs">
                            <input type="text" name="category" value="{{ $skill->category }}" required class="w-24 rounded border border-gray-300 px-2 py-1 text-xs">
                            <button class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Modifier</button>
                        </form>
                        <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10">Aucune compétence.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $skills->links() }}</div>
    </div>
@endsection
