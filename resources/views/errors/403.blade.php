<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — {{ config('app.name') }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-dark-bg text-gray-900 dark:text-dark-text min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <h1 class="text-7xl font-bold text-gray-200 dark:text-dark-border">403</h1>
        <p class="mt-4 text-lg text-gray-500 dark:text-dark-muted">Accès refusé.</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">&larr; Retour à l'accueil</a>
    </div>
</body>
</html>
