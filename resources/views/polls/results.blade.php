@extends('layouts.app')

@section('title', 'Résultats - ' . $poll->title)

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('polls.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Retour aux sondages</a>

        <h1 class="mt-6 text-3xl font-bold text-gray-900">{{ $poll->title }}</h1>
        @if($poll->description)
            <p class="mt-2 text-gray-500">{{ $poll->description }}</p>
        @endif

        <p class="mt-4 text-sm text-gray-400">{{ $totalVoters }} participant(s)</p>

        <div class="mt-10 space-y-8">
            @foreach($stats as $stat)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $stat['question']->text }}</h2>
                    <p class="text-sm text-gray-400 mt-1">{{ $stat['total_participants'] }} réponse(s)</p>

                    @if($stat['question']->type === 'text_short')
                        <div class="mt-4 space-y-2">
                            @php
                                $textAnswers = App\Models\Answer::where('question_id', $stat['question']->id)
                                    ->whereNotNull('text_response')
                                    ->latest()->get();
                            @endphp
                            @forelse($textAnswers as $answer)
                                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700">{{ $answer->text_response }}</div>
                            @empty
                                <p class="text-sm text-gray-400">Aucune réponse textuelle.</p>
                            @endforelse
                        </div>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach($stat['option_stats'] as $optStat)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700">{{ $optStat['option']->text }}</span>
                                        <span class="text-gray-400">{{ $optStat['count'] }} ({{ $optStat['percentage'] }}%)</span>
                                    </div>
                                    <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all" style="width: {{ $optStat['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
