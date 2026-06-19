{{--
    Admin top navbar: mobile menu toggle, notification bell, theme toggle, profile dropdown.
--}}
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20">
    <div class="flex items-center gap-3">
        {{-- Mobile menu toggle --}}
        <button @click="window.dispatchEvent(new Event('toggle-mobile-sidebar'))"
                class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="flex items-center gap-1.5">
        <x-ui.theme-toggle />

        {{-- Notification Bell --}}
        <div class="relative"
             x-data="{ count: 0, open: false }"
             x-init="
                fetch('{{ route('admin.notifications.unread-count') }}').then(r => r.json()).then(d => count = d.count);
                setInterval(async () => {
                    const r = await fetch('{{ route('admin.notifications.unread-count') }}');
                    const d = await r.json();
                    count = d.count;
                }, 15000);
             "
             @click.outside="open = false">
            <button @click="open = !open" class="relative w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                <i class="fas fa-bell"></i>
                <span x-show="count > 0" x-text="count > 9 ? '9+' : count" x-cloak
                      class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 flex items-center justify-center px-1"></span>
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right
                 class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">Notifications</span>
                    <span x-show="count > 0" x-text="count + ' unread'" class="text-xs text-blue-600 dark:text-blue-400"></span>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <i class="fas fa-bell mb-1.5 block text-lg text-slate-300 dark:text-slate-600"></i>
                    View all notifications
                </a>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative ml-1" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">Guidance Counselor</p>
                </div>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right
                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden py-1.5">
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700 mb-1">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-mono">{{ auth()->user()->student_id }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
