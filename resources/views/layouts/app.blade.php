<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
      x-init="dark && $el.classList.add('dark')"
      :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — Portfolio</title>
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Articles" href="{{ route('feeds.articles') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Projets" href="{{ route('feeds.projects') }}">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-dark-bg text-gray-900 dark:text-dark-text">
    {{-- Background animated orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full bg-indigo-400/20 dark:bg-indigo-600/15 blur-[100px] animate-orb-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] rounded-full bg-amber-400/20 dark:bg-amber-600/15 blur-[100px] animate-orb-slow-reverse"></div>
    </div>
    <nav class="bg-white dark:bg-dark-card border-b border-gray-200 dark:border-dark-border sticky top-0 z-50"
         x-data="{ mobileOpen: false }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight text-indigo-600 dark:text-indigo-400">
                    MbeAlex<span class="text-gray-400 dark:text-dark-muted">.</span>
                </a>
                <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('projects.index') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Projets</a>
                    <a href="{{ route('skills.index') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Compétences</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Blog</a>
                    <a href="{{ route('contact.show') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contact</a>
                    @auth
                        <a href="{{ route('polls.index') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Sondages</a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                <x-avatar :user="Auth::user()" size="sm" />
                                <span>{{ Auth::user()->pseudo }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-card rounded-lg shadow-lg dark:shadow-2xl border border-gray-100 dark:border-dark-border py-1 text-sm" style="display: none;">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 dark:text-dark-text hover:bg-gray-50 dark:hover:bg-dark-border">Mon profil</a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-dark-text hover:bg-gray-50 dark:hover:bg-dark-border">Administration</a>
                                @endif
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <x-button type="submit" variant="ghost" size="sm" class="block w-full text-left px-4 py-2">Déconnexion</x-button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.login') }}" class="text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">Connexion</a>
                        <a href="{{ route('auth.register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Inscription</a>
                    @endauth
                    <button @click="dark = !dark; $el.closest('html').classList.toggle('dark'); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                            class="p-2 rounded-lg text-gray-500 dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border transition" aria-label="Basculer le thème">
                        <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 md:hidden">
                    <button @click="dark = !dark; $el.closest('html').classList.toggle('dark'); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                            class="p-2 rounded-lg text-gray-500 dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border transition" aria-label="Basculer le thème">
                        <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                    <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-lg text-gray-600 dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
            <div x-show="mobileOpen" class="md:hidden pb-4 border-t border-gray-100 dark:border-dark-border pt-2 text-sm">
                <a href="{{ route('projects.index') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Projets</a>
                <a href="{{ route('skills.index') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Compétences</a>
                <a href="{{ route('blog.index') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Blog</a>
                <a href="{{ route('contact.show') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Contact</a>
                @auth
                    <a href="{{ route('polls.index') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Sondages</a>
                    <a href="{{ route('profile.show') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Mon profil</a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Administration</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <x-button type="submit" variant="ghost" size="sm" class="block py-2">Déconnexion</x-button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Connexion</a>
                    <a href="{{ route('auth.register') }}" class="block py-2 text-indigo-600 dark:text-indigo-400 font-medium">Inscription</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        </div>
    @endif

    <main class="relative">
        @yield('content')
    </main>

    <footer class="relative bg-white dark:bg-dark-card border-t border-gray-200 dark:border-dark-border mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-dark-muted">
                <p>&copy; {{ date('Y') }} MbeAlex. Tous droits réservés.</p>
                <div class="flex flex-wrap items-center gap-4">
                    @php
                        $siteOwner = \App\Models\User::where('role', 'admin')->first();
                        $footerLinks = $siteOwner?->social_links ?? [];
                    @endphp
                    @foreach($footerLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">{{ $link['platform'] }}</a>
                    @endforeach
                    <a href="{{ route('contact.show') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contact</a>
                    <a href="{{ route('feeds.articles') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Flux RSS Articles">RSS</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ChatBot --}}
    <div x-data="chatBot()"
         class="fixed bottom-6 right-6 z-50">
        {{-- Button --}}
        <button @click="open = !open"
                x-show="!open"
                class="bg-indigo-600 hover:bg-indigo-700 text-white p-3.5 rounded-full shadow-lg transition"
                aria-label="Ouvrir le chat">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </button>

        {{-- Panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="open = false"
             class="w-[22rem] sm:w-96 bg-white dark:bg-dark-card rounded-2xl shadow-2xl border border-gray-200 dark:border-dark-border overflow-hidden flex flex-col"
             style="display: none; max-height: 32rem;">
            {{-- Header --}}
            <div class="bg-indigo-600 dark:bg-indigo-700 text-white px-4 py-3 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="font-medium text-sm">Assistant MbeAlex</span>
                </div>
                <button @click="open = false" class="text-white/80 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-sm" x-ref="messages">
                <template x-for="(msg, i) in messages" :key="i">
                    <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                    <div :class="msg.role === 'user'
                        ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm inline-block px-3.5 py-2 max-w-[85%]'
                        : 'bg-gray-100 dark:bg-dark-border text-gray-800 dark:text-dark-text rounded-2xl rounded-bl-sm inline-block px-3.5 py-2 max-w-[85%]'">
                            <p class="whitespace-pre-wrap leading-relaxed" x-text="msg.text"></p>
                            <p x-show="msg.provider" class="text-[10px] opacity-50 mt-1 text-right" x-text="'via ' + msg.provider"></p>
                        </div>
                    </div>
                </template>
                <template x-if="loading">
                    <div class="text-left">
                        <div class="bg-gray-100 dark:bg-dark-border rounded-2xl rounded-bl-sm inline-block px-3.5 py-2">
                            <div class="flex gap-1">
                                <span class="w-2 h-2 bg-gray-400 dark:bg-dark-muted rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-2 h-2 bg-gray-400 dark:bg-dark-muted rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2 h-2 bg-gray-400 dark:bg-dark-muted rounded-full animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-ref="anchor"></div>
            </div>

            {{-- Suggestions --}}
            <div x-show="showSuggestions" class="px-4 pb-2 flex-shrink-0">
                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2">Suggestions :</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="suggestion in suggestions" :key="suggestion">
                        <button @click="sendSuggestion(suggestion)"
                                class="text-xs bg-gray-100 dark:bg-dark-border text-gray-600 dark:text-dark-muted hover:bg-indigo-100 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-1.5 rounded-full transition">
                            <span x-text="suggestion"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Error --}}
            <template x-if="error">
                <div class="px-4 pb-2">
                    <p class="text-xs text-red-500" x-text="error"></p>
                </div>
            </template>

            {{-- Input --}}
            <div class="border-t border-gray-200 dark:border-dark-border px-4 py-3 flex-shrink-0">
                <form @submit.prevent="sendMessage()" class="flex gap-2">
                    <input type="text" x-model="input" placeholder="Écrivez un message..."
                           class="flex-1 rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-bg dark:text-dark-text px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           :disabled="loading">
                    <button type="submit" :disabled="loading || !input.trim()"
                            class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 dark:disabled:bg-gray-600 text-white p-2 rounded-lg transition flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatBot', () => ({
                open: false,
                messages: [],
                input: '',
                loading: false,
                error: null,
                showSuggestions: true,
                suggestions: [
                    "C'est qui Mbe Alex ?",
                    "Il fait quoi ce site ?",
                    "Quels types de projets ?",
                    "Comment contacter MbeAlex ?",
                ],
                init() {
                    this.messages.push({
                        role: 'model',
                        text: '👋 Salut ! Je suis l\'assistant virtuel de MbeAlex. Pose-moi une question sur le portfolio, les projets, ou le créateur !'
                    });
                },
                async sendMessage(msg) {
                    const text = msg || this.input.trim();
                    if (!text || this.loading) return;
                    this.showSuggestions = false;
                    this.input = '';
                    this.error = null;
                    this.messages.push({ role: 'user', text });
                    this.loading = true;
                    this.$nextTick(() => this.scrollDown());
                    try {
                        const history = this.messages
                            .filter(m => m.role !== 'system')
                            .slice(0, -1)
                            .map(m => ({ role: m.role === 'user' ? 'user' : 'model', text: m.text }));
                        const res = await fetch('{{ route('chatbot.chat') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ message: text, history, page_title: document.title }),
                        });
                        const data = await res.json();
                        if (data.error) {
                            this.error = data.error;
                            this.messages.push({ role: 'model', text: '😕 Désolé, je n\'ai pas pu répondre. ' + data.error });
                        } else {
                            this.messages.push({ role: 'model', text: data.reply, provider: data.provider });
                        }
                    } catch (e) {
                        this.error = 'Erreur de connexion.';
                        this.messages.push({ role: 'model', text: '😕 Impossible de contacter le serveur. Réessaie plus tard.' });
                    }
                    this.loading = false;
                    this.$nextTick(() => this.scrollDown());
                },
                sendSuggestion(suggestion) {
                    this.sendMessage(suggestion);
                },
                scrollDown() {
                    const anchor = this.$refs.anchor;
                    if (anchor) anchor.scrollIntoView({ behavior: 'smooth' });
                },
            }));
        });
    </script>
</body>
</html>
