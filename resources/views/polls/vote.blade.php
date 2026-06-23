@extends('layouts.app')

@section('title', $poll->title)

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('polls.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour aux sondages</a>

        <h1 class="mt-6 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ $poll->title }}</h1>
        @if($poll->description)
            <p class="mt-2 text-gray-500 dark:text-dark-muted">{{ $poll->description }}</p>
        @endif

        <form method="POST" action="{{ route('polls.vote', $poll) }}" class="mt-10 space-y-8" x-on:submit="$el.querySelector('button[type=submit]').disabled = true">
            @csrf
            @foreach($poll->questions as $question)
                <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">
                        {{ $question->text }}
                        @if($question->is_required)
                            <span class="text-red-500 dark:text-red-400">*</span>
                        @endif
                    </h2>

                    @if($question->type === 'text_short')
                        <textarea name="question_{{ $question->id }}" rows="3"
                            class="mt-3 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            placeholder="Votre réponse...">{{ old('question_' . $question->id) }}</textarea>

                    @elseif($question->type === 'unique_choice')
                        <div class="mt-4 space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-dark-border hover:border-indigo-300 dark:hover:border-indigo-500 has-checked:border-indigo-600 dark:has-checked:border-indigo-400 has-checked:bg-indigo-50 dark:has-checked:bg-indigo-900/30 transition cursor-pointer">
                                    <input type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}"
                                        class="text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500"
                                        {{ old('question_' . $question->id) == $option->id ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-dark-text">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'multiple_choice')
                        <div class="mt-4 space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-dark-border hover:border-indigo-300 dark:hover:border-indigo-500 has-checked:border-indigo-600 dark:has-checked:border-indigo-400 has-checked:bg-indigo-50 dark:has-checked:bg-indigo-900/30 transition cursor-pointer">
                                    <input type="checkbox" name="question_{{ $question->id }}[]" value="{{ $option->id }}"
                                        class="rounded text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-dark-text">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('question_' . $question->id)
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="text-center text-sm text-gray-400 dark:text-dark-muted">
                <p>&#9888; Une fois validées, vos réponses ne pourront pas être modifiées.</p>
            </div>

            <button type="submit" class="w-full bg-indigo-600 dark:bg-indigo-500 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 dark:hover:bg-indigo-600 transition">
                Envoyer mes réponses
            </button>
        </form>
    </div>
@endsection
