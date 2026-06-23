@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    {{-- Hero --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 scroll-reveal">
        <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16">
            <div class="flex-shrink-0 scroll-reveal">
                <x-avatar :user="$admin" size="2xl" class="ring-4 ring-indigo-100 dark:ring-indigo-900" />
            </div>
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-dark-text leading-tight">
                    {{ $admin->headline ?? 'Développeur Full Stack' }}
                    @if(!str_contains($admin->headline ?? '', 'Créatif'))
                        <span class="text-indigo-600 dark:text-indigo-400">Créatif</span>
                    @endif
                </h1>
                <p class="mt-6 text-lg md:text-xl text-gray-500 dark:text-dark-muted leading-relaxed">
                    {{ $admin->bio ?? 'Je conçois des applications web modernes, performantes et élégantes. Spécialisé en Laravel, Vue.js et Tailwind CSS.' }}
                    @if($admin->social_links && collect($admin->social_links)->firstWhere('platform', 'GitHub'))
                        <span class="block mt-4 text-sm text-gray-400 dark:text-dark-muted">Les projets plus complexes sont directement sur <a href="{{ collect($admin->social_links)->firstWhere('platform', 'GitHub')['url'] }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">GitHub</a>.</span>
                    @endif
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @if($admin->social_links)
                        @foreach($admin->social_links as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition font-medium">
                                @switch(strtolower($link['platform']))
                                    @case('github')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                                        @break
                                    @case('linkedin')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                        @break
                                    @case('youtube')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        @break
                                    @case('twitter')
                                    @case('x')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        @break
                                    @case('instagram')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @endswitch
                                <span>{{ $link['platform'] }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('projects.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Voir mes projets
                    </a>
                    <a href="{{ route('contact.show') }}" class="border border-gray-300 dark:border-dark-border text-gray-700 dark:text-dark-muted px-6 py-3 rounded-lg font-medium hover:border-gray-400 dark:hover:border-dark-muted transition">
                        Me contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-white dark:bg-dark-card py-20 scroll-reveal">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-dark-text">Fonctionnalités du site</h2>
            <p class="mt-2 text-gray-500 dark:text-dark-muted">Tout ce que ce portfolio a à offrir.</p>
            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">Portfolio</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Galerie de projets classés par type : sites web, design, affiches, logos, montages vidéo.</p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">Blog</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Articles avec commentaires, tags, et validation des échanges.</p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">Sondages</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Questionnaires interactifs avec choix unique, multiples, texte ou échelle.</p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">IA intégrée</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Assistant virtuel alimenté par Gemini, Groq et OpenRouter pour répondre à vos questions.</p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">Connexion OAuth</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Inscription et connexion via Google, sans mot de passe.</p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.981l7.5-4.039a2.25 2.25 0 012.134 0l7.5 4.039a2.25 2.25 0 011.183 1.98V19.5z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-dark-text">Newsletters</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-dark-muted">Abonnez-vous pour recevoir les nouveaux articles et sondages par email.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Formations --}}
    @if($formations->isNotEmpty())
        <section class="py-20 scroll-reveal">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-dark-text">Formations</h2>
                <p class="mt-2 text-gray-500 dark:text-dark-muted">Mon parcours académique et professionnel.</p>
                <div class="mt-10 space-y-4">
                    @foreach($formations as $formation)
                        <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-5 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-dark-text">{{ $formation->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-dark-muted mt-0.5">{{ $formation->institution }} &middot; {{ $formation->year }}</p>
                            </div>
                            <span class="text-xs {{ $formation->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }} px-3 py-1 rounded-full font-medium">
                                {{ $formation->status === 'completed' ? 'Terminé' : 'En cours' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Skills --}}
    @if($skills->isNotEmpty())
        <section class="bg-white dark:bg-dark-card py-20 scroll-reveal">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-dark-text">Compétences</h2>
                <p class="mt-2 text-gray-500 dark:text-dark-muted">Les technologies avec lesquelles je travaille.</p>
                <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($skills as $category => $categorySkills)
                        <div class="scroll-reveal">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">{{ $category }}</h3>
                            <div class="space-y-3">
                                @foreach($categorySkills as $skill)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-700 dark:text-dark-muted">{{ $skill->name }}</span>
                                            <span class="text-gray-400 dark:text-dark-muted">{{ $skill->level }}/5</span>
                                        </div>
                                        <div class="h-1.5 bg-gray-100 dark:bg-dark-border rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500 dark:bg-indigo-400 rounded-full transition-all" style="width: {{ ($skill->level / 5) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Projects --}}
    @if($projects->isNotEmpty())
        <section class="py-20 scroll-reveal">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-dark-text">Projets récents</h2>
                        <p class="mt-2 text-gray-500 dark:text-dark-muted">Sites statiques, design, affiches, logo, montage vidéo — une sélection de mes travaux.</p>
                    </div>
                    <a href="{{ route('projects.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium text-sm">Voir tout →</a>
                </div>
                <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="group bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden hover:shadow-lg dark:hover:shadow-2xl transition scroll-reveal">
                            <div class="aspect-video bg-gray-100 dark:bg-dark-bg flex items-center justify-center text-gray-300 dark:text-dark-muted text-sm">
                                @if($project->image_url)
                                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                @endif
                            </div>
                            <div class="p-5">
                                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                    @switch($project->type)
                                        @case('web_static') Site web statique @break
                                        @case('design') Design @break
                                        @case('affiche') Affiche @break
                                        @case('logo') Logo @break
                                        @case('montage_video') Montage vidéo @break
                                        @default {{ str_replace('_', ' ', $project->type) }}
                                    @endswitch
                                </span>
                                <h3 class="mt-1 font-semibold text-gray-900 dark:text-dark-text group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $project->title }}</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-dark-muted line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                                @if($project->tags->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach($project->tags as $tag)
                                            <span class="text-xs bg-gray-100 dark:bg-dark-border text-gray-600 dark:text-dark-muted px-2 py-0.5 rounded">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
