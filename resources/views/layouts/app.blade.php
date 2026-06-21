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
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-dark-bg text-gray-900 dark:text-dark-text">
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
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-dark-text hover:bg-gray-50 dark:hover:bg-dark-border">Déconnexion</button>
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
                        <button class="block py-2 text-gray-600 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400">Déconnexion</button>
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

    <main>
        @yield('content')
    </main>

    <footer class="bg-white dark:bg-dark-card border-t border-gray-200 dark:border-dark-border mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-dark-muted">
                <p>&copy; {{ date('Y') }} MbeAlex. Tous droits réservés.</p>
                <div class="flex gap-6">
                    @php
                        $siteOwner = \App\Models\User::where('role', 'admin')->first();
                        $footerLinks = $siteOwner?->social_links ?? [];
                    @endphp
                    @foreach($footerLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">{{ $link['platform'] }}</a>
                    @endforeach
                    <a href="{{ route('contact.show') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
