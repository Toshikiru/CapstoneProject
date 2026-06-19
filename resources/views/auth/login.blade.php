<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — TPC Entrance Exam System</title>
    <script>
        (function(){
            const s=localStorage.getItem('theme'),p=window.matchMedia('(prefers-color-scheme: dark)').matches;
            if(s==='dark'||(s===null&&p)) document.documentElement.classList.add('dark');
        })();
        window.toggleTheme=function(){const d=document.documentElement.classList.toggle('dark');localStorage.setItem('theme',d?'dark':'light');};
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class'}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-800 via-blue-900 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex items-center justify-center p-4 transition-colors">

{{-- Theme toggle --}}
<div class="fixed top-4 right-4">
    <button onclick="window.toggleTheme()" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" title="Toggle dark mode">
        <i class="fas fa-sun dark:hidden"></i>
        <i class="fas fa-moon hidden dark:block"></i>
    </button>
</div>

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/20 shadow-lg">
            <i class="fas fa-graduation-cap text-white text-3xl"></i>
        </div>
        <h1 class="text-white text-2xl font-bold">Talibon Polytechnic College</h1>
        <p class="text-blue-300 text-sm mt-1">Entrance Examination &amp; Student Profile System</p>
        <p class="text-blue-400 text-xs mt-1">Guidance Services Office</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8 border border-transparent dark:border-slate-700">
        <h2 class="text-slate-800 dark:text-slate-100 text-xl font-bold mb-1">Sign In</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Enter your credentials to continue.</p>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 rounded-xl px-4 py-3 text-sm">
                @foreach($errors->all() as $error)
                    <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" x-data="{ showPass: false }">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Student ID / Admin ID</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-id-card"></i></span>
                    <input type="text" name="student_id" value="{{ old('student_id') }}" autofocus required
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('student_id') border-red-400 @enderror"
                           placeholder="e.g. 2024-0001">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-lock"></i></span>
                    <input :type="showPass?'text':'password'" name="password" required
                           class="w-full pl-10 pr-10 py-2.5 border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter your password">
                    <button type="button" @click="showPass=!showPass"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <i :class="showPass?'fas fa-eye-slash':'fas fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right-to-bracket mr-2"></i>Sign In
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700 text-center">
            <p class="text-slate-400 dark:text-slate-500 text-xs">Trouble logging in? Contact the Guidance Office.</p>
        </div>
    </div>

    <p class="text-center text-blue-400 dark:text-slate-600 text-xs mt-4">
        &copy; {{ date('Y') }} Talibon Polytechnic College — Guidance Services
    </p>
</div>
</body>
</html>
