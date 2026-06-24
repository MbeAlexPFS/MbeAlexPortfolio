<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ config('app.name') }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-dark-bg text-gray-900 dark:text-dark-text min-h-screen flex items-center justify-center">
    <div class="text-center px-4 max-w-md w-full">
        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="h-16 mx-auto mb-6 opacity-80">

        <h1 class="text-3xl font-bold text-gray-900 dark:text-dark-text">Site en maintenance</h1>

        <p class="mt-3 text-gray-500 dark:text-dark-muted">
            Le site est en cours de maintenance. Revenez bientôt&nbsp;!
        </p>

        <div class="mt-8 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl p-6 text-left">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-dark-text mb-4 text-center">Accès administrateur</h2>

            @if($errors->any())
                <div class="mb-4 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg px-3 py-2">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('maintenance.login') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-bg dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Mot de passe</label>
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-bg dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <x-button type="submit" variant="primary" size="lg" class="w-full" loading-text="Connexion...">
                        Connexion
                    </x-button>
                </div>
            </form>
        </div>

        <p class="mt-8 text-xs text-gray-400 dark:text-dark-muted">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
