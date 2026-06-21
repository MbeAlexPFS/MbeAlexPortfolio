@extends('layouts.app')

@section('title', 'Gérer les sondages')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Administration</a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold text-gray-900">Sondages</h1>
            <a href="{{ route('admin.polls.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Nouveau sondage</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse($polls as $poll)
                <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="{{ $poll->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded">
                                {{ $poll->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            <span class="text-gray-400">{{ $poll->questions_count }} question(s)</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mt-1">{{ $poll->title }}</h3>
                        @if($poll->description)
                            <p class="text-sm text-gray-500">{{ Str::limit($poll->description, 80) }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.polls.edit', $poll) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Gérer</a>
                        <form method="POST" action="{{ route('admin.polls.destroy', $poll) }}" onsubmit="return confirm('Supprimer ce sondage ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10">Aucun sondage.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $polls->links() }}</div>
    </div>
@endsection
