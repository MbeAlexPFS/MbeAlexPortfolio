@extends('layouts.app')

@section('title', 'Administration')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Administration</h1>
        <p class="mt-2 text-gray-500 dark:text-dark-muted">Gérez votre portfolio.</p>

        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                <p class="text-2xl font-bold text-gray-900 dark:text-dark-text">{{ $stats['users'] }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">Utilisateurs</p>
            </div>
            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                <p class="text-2xl font-bold text-gray-900 dark:text-dark-text">{{ $stats['articles'] }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">Articles</p>
            </div>
            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending_comments'] }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">Commentaires en attente</p>
            </div>
            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['unread_messages'] }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">Messages non lus</p>
            </div>
            <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5">
                <p class="text-2xl font-bold text-gray-900 dark:text-dark-text">{{ $stats['active_polls'] }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">Sondages actifs</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.users') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer les utilisateurs</a>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer les projets</a>
            <a href="{{ route('admin.skills.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer les compétences</a>
            <a href="{{ route('admin.blog.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer le blog</a>
            <a href="{{ route('admin.polls.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer les sondages</a>
            <a href="{{ route('admin.contact.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Messages reçus</a>
            <a href="{{ route('admin.formations.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Gérer les formations</a>
            <a href="{{ route('admin.profile.edit') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Profil du site</a>
            <a href="{{ route('admin.cv') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">Télécharger mon CV (PDF)</a>
        </div>

        <div class="mt-6 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-dark-text">Mode maintenance</p>
                <p class="text-xs text-gray-500 dark:text-dark-muted mt-0.5">
                    @if($maintenance)
                        Actif — les visiteurs voient une page de maintenance. Vous y avez toujours accès.
                    @else
                        Inactif — le site est accessible à tous.
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.toggle') }}">
                @csrf
                <x-button type="submit" variant="{{ $maintenance ? 'success' : 'warning' }}" size="md" loading-text="...">
                    {{ $maintenance ? 'Désactiver' : 'Activer' }}
                </x-button>
            </form>
        </div>

        <div class="mt-10 grid lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Commentaires en attente</h2>
                <div class="mt-4 space-y-3">
                    @forelse($pendingComments as $comment)
                        <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-4">
                            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-dark-muted">
                                <span>{{ $comment->user->pseudo }} sur "{{ Str::limit($comment->article->title, 40) }}"</span>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-dark-muted">{{ Str::limit($comment->content, 100) }}</p>
                            <div class="mt-2 flex gap-2">
                                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                                    @csrf
                                    @method('PUT')
                                    <x-button type="submit" variant="success" size="sm" loading-text="...">Approuver</x-button>
                                </form>
                                <form method="POST" action="{{ route('admin.comments.reject', $comment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm" loading-text="...">Rejeter</x-button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-dark-muted">Aucun commentaire en attente.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-dark-text">Derniers messages</h2>
                <div class="mt-4 space-y-3">
                    @forelse($latestMessages as $message)
                        <a href="{{ route('admin.contact.show', $message) }}" class="block bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-4 hover:shadow-sm dark:hover:shadow-2xl transition">
                            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-dark-muted">
                                <span class="font-medium text-gray-700 dark:text-dark-text">{{ $message->name }}</span>
                                <span>{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-dark-text">{{ $message->subject }}</p>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-dark-muted">{{ Str::limit($message->message, 80) }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-dark-muted">Aucun message.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
