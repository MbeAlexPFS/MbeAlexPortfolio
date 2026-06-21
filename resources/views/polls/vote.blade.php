@extends('layouts.app')

@section('title', $poll->title)

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('polls.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Retour aux sondages</a>

        <h1 class="mt-6 text-3xl font-bold text-gray-900">{{ $poll->title }}</h1>
        @if($poll->description)
            <p class="mt-2 text-gray-500">{{ $poll->description }}</p>
        @endif

        <form method="POST" action="{{ route('polls.vote', $poll) }}" class="mt-10 space-y-8">
            @csrf
            @foreach($poll->questions as $question)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $question->text }}
                        @if($question->is_required)
                            <span class="text-red-500">*</span>
                        @endif
                    </h2>

                    @if($question->type === 'text_short')
                        <textarea name="question_{{ $question->id }}" rows="3"
                            class="mt-3 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            placeholder="Votre réponse...">{{ old('question_' . $question->id) }}</textarea>

                    @elseif($question->type === 'unique_choice')
                        <div class="mt-4 space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 has-checked:border-indigo-600 has-checked:bg-indigo-50 transition cursor-pointer">
                                    <input type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}"
                                        class="text-indigo-600 focus:ring-indigo-500"
                                        {{ old('question_' . $question->id) == $option->id ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'multiple_choice')
                        <div class="mt-4 space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 has-checked:border-indigo-600 has-checked:bg-indigo-50 transition cursor-pointer">
                                    <input type="checkbox" name="question_{{ $question->id }}[]" value="{{ $option->id }}"
                                        class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('question_' . $question->id)
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 transition">
                Envoyer mes réponses
            </button>
        </form>
    </div>
@endsection
