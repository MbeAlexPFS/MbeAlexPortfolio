@extends('layouts.app')

@section('title', 'Profil du site')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">Profil du site</h1>
        <p class="mt-1 text-gray-500 dark:text-dark-muted">Configurez les informations affichées sur la page d'accueil.</p>

        <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-8 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="headline" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Titre / Accroche</label>
                <input type="text" name="headline" id="headline" value="{{ old('headline', $user->headline ?? 'Développeur Full Stack Créatif') }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('headline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-dark-text">À propos de moi</label>
                <textarea name="bio" id="bio" rows="6"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('bio', $user->bio ?? '') }}</textarea>
                @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div x-data="socialLinks()">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Liens externes</label>
                    <button type="button" @click="addLink()" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Ajouter un lien</button>
                </div>
                <p class="mt-1 text-xs text-gray-400 dark:text-dark-muted">Plateformes : GitHub, LinkedIn, YouTube, Twitter, Instagram, etc.</p>

                <template x-for="(link, index) in links" :key="index">
                    <div class="mt-3 flex items-start gap-3">
                        <div class="flex-1">
                            <input type="text" :name="`social_links[${index}][platform]`" x-model="link.platform" placeholder="Plateforme (GitHub, LinkedIn...)"
                                class="block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <input type="url" :name="`social_links[${index}][url]`" x-model="link.url" placeholder="https://..."
                                class="block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <button type="button" @click="removeLink(index)" class="mt-1.5 text-gray-400 hover:text-red-500 transition flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>

                @error('social_links') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <x-button type="submit" variant="primary" size="lg" loading-text="Enregistrement...">
                Enregistrer
            </x-button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function socialLinks() {
        const initial = @json(old('social_links', $user->social_links ?? []));
        return {
            links: initial.length ? initial : [{ platform: 'GitHub', url: '' }],
            addLink() {
                this.links.push({ platform: '', url: '' });
            },
            removeLink(index) {
                this.links.splice(index, 1);
            }
        };
    }
</script>
@endpush
