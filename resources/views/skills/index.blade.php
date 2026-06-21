@extends('layouts.app')

@section('title', 'Compétences')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Compétences</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Les technologies et outils que je maîtrise.</p>

        <div class="mt-10 space-y-12">
            @forelse($skills as $category => $categorySkills)
                <div class="scroll-reveal">
                    <h2 class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">{{ $category }}</h2>
                    <div class="mt-4 grid sm:grid-cols-2 gap-4">
                        @foreach($categorySkills as $skill)
                            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-medium text-gray-900 dark:text-dark-text">{{ $skill->name }}</h3>
                                    <span class="text-sm text-gray-400 dark:text-dark-muted">{{ $skill->level }}/5</span>
                                </div>
                                <div class="h-2 bg-gray-100 dark:bg-dark-border rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 dark:bg-indigo-400 rounded-full transition-all" style="width: {{ ($skill->level / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 dark:text-dark-muted py-20">Aucune compétence renseignée.</p>
            @endforelse
        </div>
    </div>
@endsection
