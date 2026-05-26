<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Manajemen Deploy') }} — Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            if (localStorage.getItem('theme') !== 'light') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="h-full font-sans text-slate-900 antialiased bg-slate-100 dark:bg-slate-950 dark:text-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Theme Toggle Switch (Optional, but good for login page too) -->
        <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
            <button id="theme-toggle" onclick="toggleTheme()" title="Ganti tema"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors shadow-sm">
                <svg id="icon-sun" class="w-5 h-5" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
                <svg id="icon-moon" class="w-5 h-5" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>

        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Deploy Manager</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Production Release System</p>
        </div>

        <!-- Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
            <div class="p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
        
        <div class="mt-8 text-center text-xs text-slate-500 dark:text-slate-500">
            &copy; {{ date('Y') }} Manajemen Deploy
        </div>
    </div>

    <script>
        function applyTheme(theme) {
            const html = document.documentElement;
            const sun = document.getElementById('icon-sun');
            const moon = document.getElementById('icon-moon');
            
            if (theme === 'dark') {
                html.classList.add('dark');
                if (sun) sun.style.display = 'inline-block';
                if (moon) moon.style.display = 'none';
            } else {
                html.classList.remove('dark');
                if (sun) sun.style.display = 'none';
                if (moon) moon.style.display = 'inline-block';
            }
        }

        function toggleTheme() {
            const next = localStorage.getItem('theme') === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', next);
            applyTheme(next);
        }

        (function () {
            const theme = localStorage.getItem('theme') === 'light' ? 'light' : 'dark';
            applyTheme(theme);
        })();
    </script>
</body>
</html>
