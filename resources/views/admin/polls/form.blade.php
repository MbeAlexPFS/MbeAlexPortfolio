@extends('layouts.app')

@section('title', isset($poll) ? 'Gérer le sondage' : 'Nouveau sondage')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.polls.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ isset($poll) ? 'Gérer le sondage' : 'Nouveau sondage' }}</h1>

        <form method="POST" action="{{ isset($poll) ? route('admin.polls.update', $poll) : route('admin.polls.store') }}" class="mt-8 space-y-5">
            @csrf
            @if(isset($poll)) @method('PUT') @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $poll->title ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $poll->description ?? '') }}</textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Date de début</label>
                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', isset($poll) && $poll->start_date ? $poll->start_date->format('Y-m-d\TH:i') : '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Date de fin</label>
                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', isset($poll) && $poll->end_date ? $poll->end_date->format('Y-m-d\TH:i') : '') }}"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                    {{ old('is_active', $poll->is_active ?? true) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-dark-text">Actif</label>
            </div>

            <x-button type="submit" variant="primary" size="lg" loading-text="Enregistrement...">
                {{ isset($poll) ? 'Mettre à jour' : 'Créer le sondage' }}
            </x-button>
        </form>

        @if(isset($poll))
            <hr class="my-12 border-gray-200 dark:border-dark-border">

            <h2 class="text-xl font-semibold text-gray-900 dark:text-dark-text">Questions</h2>

            <div class="mt-6 space-y-4">
                @foreach($poll->questions as $question)
                    <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ str_replace('_', ' ', $question->type) }}</span>
                                <h3 class="font-medium text-gray-900 dark:text-dark-text mt-0.5">{{ $question->text }}</h3>
                                @if($question->options->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach($question->options as $opt)
                                            <span class="text-xs bg-gray-100 dark:bg-dark-border text-gray-600 dark:text-dark-muted px-2 py-0.5 rounded">{{ $opt->text }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.polls.questions.destroy', $question) }}">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger" size="sm" loading-text="...">Supprimer</x-button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Ajouter une question</h3>
                <form method="POST" action="{{ route('admin.polls.questions.store', $poll) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="text" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Texte de la question</label>
                        <input type="text" name="text" id="text" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Type</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="unique_choice">Choix unique</option>
                            <option value="multiple_choice">Choix multiples</option>
                            <option value="text_short">Texte court</option>
                            <option value="scale">Échelle de notation</option>
                        </select>
                    </div>
                    <div id="options-field" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Options (une par ligne)</label>
                        <textarea name="options" rows="4"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                    </div>
                    <div id="scale-fields" class="hidden grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="scale_min" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Note minimale</label>
                            <input type="number" name="scale_min" id="scale_min" value="1" min="1"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="scale_max" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Note maximale</label>
                            <input type="number" name="scale_max" id="scale_max" value="5" min="2" max="10"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_required" value="1" id="is_required" checked
                            class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                        <label for="is_required" class="text-sm text-gray-700 dark:text-dark-text">Obligatoire</label>
                    </div>
                    <x-button type="submit" variant="primary" size="md" loading-text="Ajout...">
                        Ajouter la question
                    </x-button>
                </form>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.getElementById('type')?.addEventListener('change', function() {
            const type = this.value;
            document.getElementById('options-field').style.display = (type === 'unique_choice' || type === 'multiple_choice') ? 'block' : 'none';
            document.getElementById('scale-fields').style.display = type === 'scale' ? 'grid' : 'none';
        });
    </script>
    @endpush
@endsection
