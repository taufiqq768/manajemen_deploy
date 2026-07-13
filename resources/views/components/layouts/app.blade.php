<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem manajemen pengajuan dan persetujuan deploy aplikasi ke production.">
    <title>{{ isset($title) ? $title . ' — ' : '' }}Manajemen Deploy</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Prevent flash of wrong theme --}}
    <script>
        (function () {
            if (localStorage.getItem('theme') !== 'light') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body
    class="h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased transition-colors duration-200">

    {{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
    <div class="flex h-full">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                            clip-rule="evenodd" />
                        <path
                            d="M5.26 17.242a.75.75 0 1 0-.897-1.203 5.243 5.243 0 0 0-2.05 5.022.75.75 0 0 0 .625.627 5.243 5.243 0 0 0 5.022-2.051.75.75 0 1 0-1.202-.897 3.744 3.744 0 0 1-3.008 1.51c0-1.23.592-2.323 1.51-3.008Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white leading-tight">Deploy Manager</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Production System</p>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @php $user = auth()->user(); @endphp

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-bold tracking-wider text-slate-400 uppercase">Deploy System</p>
                </div>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('deploy-requests.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ (request()->routeIs('deploy-requests.*') && !request()->routeIs('deploy-requests.create')) ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Deploy Requests
                </a>

                <a href="{{ route('version-logs.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('version-logs.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Log Update Versi
                </a>

                @if($user->isProgrammer())
                    <a href="{{ route('deploy-requests.create') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                  {{ request()->routeIs('deploy-requests.create') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Deploy
                    </a>
                @endif

                @if($user->isAdmin())
                    <a href="{{ route('applications.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                          {{ request()->routeIs('applications.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Aplikasi
                    </a>
                @endif

                @if($user->isAdmin())
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                          {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manajemen User
                    </a>

                    <a href="{{ route('waha-connection.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                          {{ request()->routeIs('waha-connection.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Cek Koneksi WAHA
                    </a>
                @endif


                <a href="{{ route('notifications.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('notifications.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <div class="relative flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php $unread = $user->unreadNotificationsCount(); @endphp
                        @if($unread > 0)
                            <span
                                class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 rounded-full text-[10px] flex items-center justify-center text-white font-bold">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </div>
                    Notifikasi
                    @if($unread > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unread }}</span>
                    @endif
                </a>

                {{-- IT Work Hub Section --}}
                @if(auth()->check())
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-bold tracking-wider text-slate-400 uppercase">IT Work Hub</p>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isProjectManager())
                        <a href="{{ route('it-work-hub.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                  {{ request()->routeIs('it-work-hub.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-chart-bar text-lg flex-shrink-0"></i>
                            Dashboard
                        </a>

                        <a href="{{ route('it-work-hub.longlist') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                  {{ request()->routeIs('it-work-hub.longlist', 'it-work-hub.create', 'it-work-hub.show', 'it-work-hub.activities') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-code text-lg flex-shrink-0"></i>
                            App Dev
                        </a>

                        <a href="{{ route('it-work-hub.non-app.longlist') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                  {{ request()->routeIs('it-work-hub.non-app.longlist', 'it-work-hub.non-app.create', 'it-work-hub.non-app.show', 'it-work-hub.non-app.activities') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-briefcase text-lg flex-shrink-0"></i>
                            Non App
                        </a>

                        <a href="{{ route('it-work-hub.governance.longlist') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                {{ request()->routeIs('it-work-hub.governance.*') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-shield-check text-lg flex-shrink-0"></i>
                            Governance
                        </a>

                        <a href="{{ route('it-work-hub.project-groups') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                  {{ request()->routeIs('it-work-hub.project-groups') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-layers-linked text-lg flex-shrink-0"></i>
                            Project Grouping
                        </a>

                        <a href="{{ route('it-work-hub.repository') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                                  {{ request()->routeIs('it-work-hub.repository') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-file-text text-lg flex-shrink-0"></i>
                            Repository Doc
                        </a>
                    @endif

                    <a href="{{ route('it-work-hub.todo') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                                      {{ request()->routeIs('it-work-hub.todo') ? 'bg-indigo-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ti ti-checklist text-lg flex-shrink-0"></i>
                        To-Do List
                    </a>

                @endif

            </nav>

            {{-- User info --}}
            <div class="border-t border-slate-200 dark:border-slate-800 px-4 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            @if($user->role === 'project_manager') Project Manager
                            @elseif($user->role === 'admin') Administrator
                            @else Programmer
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('profile.edit') }}" title="Ganti Password"
                            class="p-1.5 text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout"
                                class="p-1.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Overlay mobile --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/60 lg:hidden hidden" onclick="toggleSidebar()">
        </div>

        {{-- ═══════════════════════════ MAIN ═══════════════════════════ --}}
        <div class="flex-1 flex flex-col lg:ml-64 min-h-full min-w-0">

            {{-- Top bar --}}
            <header
                class="sticky top-0 z-30 bg-white/80 dark:bg-slate-950/80 backdrop-blur border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3 flex items-center gap-4">
                <button onclick="toggleSidebar()"
                    class="lg:hidden text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    {{ $title ?? 'Dashboard' }}
                </h1>

                <div class="ml-auto flex items-center gap-3">
                    {{-- Role badge --}}
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                      @if($user->isAdmin()) bg-red-500/20 text-red-700 dark:text-red-300 border border-red-500/30
                      @elseif($user->isProjectManager()) bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-500/30
                      @else bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30
                      @endif">
                        <span class="w-1.5 h-1.5 rounded-full
                        @if($user->isAdmin()) bg-red-400
                        @elseif($user->isProjectManager()) bg-purple-400
                        @else bg-indigo-400
                        @endif"></span>
                        @if($user->isAdmin()) Administrator
                        @elseif($user->isProjectManager()) Project Manager
                        @else Programmer
                        @endif
                    </span>

                    {{-- Theme toggle button --}}
                    <button id="theme-toggle" onclick="toggleTheme()" title="Ganti tema" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;
                               border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;
                               border:1px solid #334155;background:#1e293b;color:#f8fafc;
                               transition:all .2s">
                        <svg id="icon-sun" style="width:14px;height:14px;flex-shrink:0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg id="icon-moon" style="width:14px;height:14px;flex-shrink:0;display:none" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span id="theme-label">Terang</span>
                    </button>
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="px-4 sm:px-6 pt-4 space-y-2">
                @if(session('success'))
                    <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-600 dark:text-green-400 text-sm"
                        role="alert">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm"
                        role="alert">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 sm:px-6 py-6">
                {{ $slot }}
            </main>

            <footer
                class="px-4 sm:px-6 py-4 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-400 dark:text-slate-600 text-center">
                © {{ date('Y') }} Manajemen Deploy — Production Release System
            </footer>
        </div>
    </div>

    <script>
        /* ── Sidebar ── */
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        /* ── Theme ── */
        function applyTheme(theme) {
            const html = document.documentElement;
            const btn = document.getElementById('theme-toggle');
            const sun = document.getElementById('icon-sun');
            const moon = document.getElementById('icon-moon');
            const label = document.getElementById('theme-label');

            if (theme === 'dark') {
                html.classList.add('dark');
                btn.style.background = '#1e293b';
                btn.style.borderColor = '#334155';
                btn.style.color = '#f8fafc';
                sun.style.display = 'inline-block';
                moon.style.display = 'none';
                if (label) label.textContent = 'Terang';
            } else {
                html.classList.remove('dark');
                btn.style.background = '#f1f5f9';
                btn.style.borderColor = '#cbd5e1';
                btn.style.color = '#1e293b';
                sun.style.display = 'none';
                moon.style.display = 'inline-block';
                if (label) label.textContent = 'Gelap';
            }
        }

        function toggleTheme() {
            const next = localStorage.getItem('theme') === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', next);
            applyTheme(next);
        }

        // Run immediately — script is at end of body, elements exist
        (function () {
            const theme = localStorage.getItem('theme') === 'light' ? 'light' : 'dark';
            applyTheme(theme);
        })();
    </script>
    @stack('scripts')
</body>

</html>