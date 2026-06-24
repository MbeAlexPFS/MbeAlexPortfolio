@extends('layouts.app')

@section('title', 'Gérer les utilisateurs')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Administration</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Utilisateurs</h1>

        <div class="mt-8 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-dark-border text-left text-gray-500 dark:text-dark-muted">
                        <th class="pb-3 font-medium"></th>
                        <th class="pb-3 font-medium">Pseudo</th>
                        <th class="pb-3 font-medium">Email</th>
                        <th class="pb-3 font-medium">Rôle</th>
                        <th class="pb-3 font-medium">Vérifié</th>
                        <th class="pb-3 font-medium">Actif</th>
                        <th class="pb-3 font-medium">Inscrit le</th>
                        <th class="pb-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b border-gray-100 dark:border-dark-border">
                            <td class="py-3 pr-2">
                                <x-avatar :user="$user" size="sm" />
                            </td>
                            <td class="py-3 text-gray-900 dark:text-dark-text">{{ $user->pseudo }}</td>
                            <td class="py-3 text-gray-600 dark:text-dark-muted">{{ $user->email }}</td>
                            <td class="py-3">
                                <span class="text-xs {{ $user->isAdmin() ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-dark-muted' }} px-2 py-0.5 rounded-full">{{ $user->role }}</span>
                            </td>
                            <td class="py-3 text-gray-900 dark:text-dark-text">{{ $user->is_verified ? 'Oui' : 'Non' }}</td>
                            <td class="py-3 text-gray-900 dark:text-dark-text">{{ $user->is_active ? 'Oui' : 'Non' }}</td>
                            <td class="py-3 text-gray-400 dark:text-dark-muted text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    <x-button type="submit" variant="{{ $user->is_active ? 'danger' : 'success' }}" size="sm" loading-text="...">
                                        {{ $user->is_active ? 'Bannir' : 'Réactiver' }}
                                    </x-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $users->links() }}
        </div>
    </div>
@endsection
