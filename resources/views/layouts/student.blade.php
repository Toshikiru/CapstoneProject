<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student') — TPC Entrance Exam</title>

    <script>
        (function(){
            const s = localStorage.getItem('theme');
            const p = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if(s==='dark'||(s===null&&p)) document.documentElement.classList.add('dark');
        })();
        window.toggleTheme = function(){
            const d = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', d?'dark':'light');
        };
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>[x-cloak] { display: none !important; }</style>
    @stack('head')
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-200 transition-colors min-h-screen">

<nav class="bg-blue-700 dark:bg-slate-900 border-b border-blue-800 dark:border-slate-700 h-16 flex items-center justify-between px-4 lg:px-8 shadow sticky top-0 z-20">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 dark:bg-blue-600 rounded-xl flex items-center justify-center">
            <i class="fas fa-graduation-cap text-white"></i>
        </div>
        <div>
            <p class="font-bold text-sm text-white">TPC Entrance Exam System</p>
            <p class="text-blue-200 dark:text-slate-400 text-xs">Student Portal</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <x-ui.theme-toggle />
        <span class="text-blue-200 dark:text-slate-400 text-sm hidden sm:block mr-2">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center gap-1.5 text-blue-200 dark:text-slate-400 hover:text-white dark:hover:text-white text-sm px-3 py-1.5 rounded-lg hover:bg-white/10 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span class="hidden sm:inline">Log out</span>
            </button>
        </form>
    </div>
</nav>

<main class="max-w-4xl mx-auto p-4 lg:p-6">
    <x-ui.flash-messages />
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
