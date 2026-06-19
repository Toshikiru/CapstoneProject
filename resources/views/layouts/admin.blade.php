<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TPC Guidance System</title>

    {{-- Apply saved theme before first paint to avoid a flash of the wrong theme --}}
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
        window.toggleTheme = function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        };
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        [x-cloak] { display: none !important; }
        .pulse-live { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-200 transition-colors">

<x-layout.admin-sidebar />

<div class="lg:ml-64 transition-all duration-200 min-h-screen flex flex-col">
    <x-layout.admin-navbar />

    <main class="flex-1 p-4 lg:p-6 max-w-[1600px] w-full mx-auto">
        <x-ui.flash-messages />
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
