@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Contact</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Une question, un projet ? N'hésitez pas à me contacter.</p>

        <form method="POST" action="{{ route('contact.store') }}" class="mt-10 space-y-5">
            @csrf
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Nom</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Sujet</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('subject') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-dark-muted">Message</label>
                <textarea name="message" id="message" rows="6" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border bg-white dark:bg-dark-bg px-3 py-2.5 text-sm text-gray-900 dark:text-dark-text shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <x-button type="submit" variant="primary" size="lg" loading-text="Envoi...">
                Envoyer le message
            </x-button>
        </form>
    </div>
@endsection
