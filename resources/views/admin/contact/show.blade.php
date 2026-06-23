@extends('layouts.app')

@section('title', $message->subject)

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.contact.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Messages</a>

        <div class="mt-8 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-dark-text">{{ $message->subject }}</h1>
            <div class="mt-4 flex items-center gap-4 text-sm text-gray-500 dark:text-dark-muted">
                <span class="font-medium text-gray-700 dark:text-dark-text">{{ $message->name }}</span>
                <a href="mailto:{{ $message->email }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">{{ $message->email }}</a>
                <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="mt-8 prose prose-gray dark:prose-invert max-w-none">
                {!! nl2br($message->message) !!}
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <a href="mailto:{{ $message->email }}" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">Répondre</a>
            <form method="POST" action="{{ route('admin.contact.destroy', $message) }}" onsubmit="return confirm('Supprimer ce message ?')">
                @csrf
                @method('DELETE')
                <button class="border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition">Supprimer</button>
            </form>
        </div>
    </div>
@endsection
