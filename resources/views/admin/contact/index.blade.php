@extends('layouts.app')

@section('title', 'Messages reçus')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Messages reçus</h1>

        <div class="mt-8 space-y-4">
            @forelse($messages as $message)
                <a href="{{ route('admin.contact.show', $message) }}" class="block bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 hover:shadow-md dark:hover:shadow-2xl transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(!$message->is_read)
                                <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                            @endif
                            <h3 class="font-semibold text-gray-900 dark:text-dark-text">{{ $message->subject }}</h3>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-dark-muted">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-3 text-sm text-gray-500 dark:text-dark-muted">
                        <span>{{ $message->name }}</span>
                        <span>&middot;</span>
                        <span>{{ $message->email }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-dark-muted line-clamp-2">{{ $message->message }}</p>
                </a>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-10">Aucun message.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $messages->links() }}</div>
    </div>
@endsection
