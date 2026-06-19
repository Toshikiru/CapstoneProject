{{--
    Admin sidebar — fixed, collapsible, responsive.
    Collapsed/expanded state persists in localStorage via Alpine + x-init.
    On mobile (<lg), the sidebar becomes an off-canvas drawer toggled by the navbar's hamburger button,
    which dispatches a 'toggle-mobile-sidebar' event that this component listens for.
--}}
@php
$navGroups = [
    [
        'label' => null,
        'items' => [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'fa-gauge', 'label' => 'Dashboard'],
        ],
    ],
    [
        'label' => 'Examination',
        'items' => [
            ['route' => 'admin.students.index', 'pattern' => 'admin.students.*', 'icon' => 'fa-user-graduate', 'label' => 'Students'],
            ['route' => 'admin.exams.index', 'pattern' => 'admin.exams.*', 'icon' => 'fa-file-pen', 'label' => 'Examinations'],
            ['route' => 'admin.monitoring.index', 'pattern' => 'admin.monitoring.*', 'icon' => 'fa-display', 'label' => 'Live Monitoring'],
            ['route' => 'admin.results.index', 'pattern' => 'admin.results.*', 'icon' => 'fa-chart-simple', 'label' => 'Results'],
        ],
    ],
    [
        'label' => 'System',
        'items' => [
            ['route' => 'admin.notifications.index', 'pattern' => 'admin.notifications.*', 'icon' => 'fa-bell', 'label' => 'Notifications'],
            ['route' => 'admin.backup.index', 'pattern' => 'admin.backup.*', 'icon' => 'fa-database', 'label' => 'Backup'],
        ],
    ],
];
@endphp

<div x-data="{
        collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        mobileOpen: false,
        isDesktop: window.innerWidth >= 1024,
    }"
    x-init="
        $watch('collapsed', v => localStorage.setItem('sidebar_collapsed', v));
        window.addEventListener('toggle-mobile-sidebar', () => mobileOpen = !mobileOpen);
        window.addEventListener('resize', () => { isDesktop = window.innerWidth >= 1024; });
    "
>
    <aside
        :class="collapsed ? 'lg:w-[72px]' : 'lg:w-64'"
        :style="(mobileOpen || isDesktop) ? 'transform: translateX(0)' : 'transform: translateX(-100%)'"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 dark:bg-slate-950 flex flex-col transition-all duration-200 ease-in-out"
    >
        {{-- Brand --}}
        <div class="h-16 flex items-center gap-3 px-4 border-b border-white/10 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-graduation-cap text-white text-sm"></i>
            </div>
            <div x-show="!collapsed" class="overflow-hidden whitespace-nowrap transition-all duration-150">
                <p class="text-white font-bold text-sm leading-tight">TPC Guidance</p>
                <p class="text-slate-400 text-[11px] leading-tight">Counselor Portal</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
            @foreach($navGroups as $group)
                <div>
                    @if($group['label'])
                        <p x-show="!collapsed" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            {{ $group['label'] }}
                        </p>
                    @endif
                    <div class="space-y-1">
                        @foreach($group['items'] as $item)
                            @php $active = request()->routeIs($item['pattern']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                                      {{ $active
                                            ? 'bg-blue-600 text-white shadow-sm shadow-blue-900/50'
                                            : 'text-slate-300 hover:bg-white/5 hover:text-white' }}"
                               :title="collapsed ? '{{ $item['label'] }}' : ''">
                                <i class="fas {{ $item['icon'] }} w-5 text-center text-[15px] flex-shrink-0"></i>
                                <span x-show="!collapsed" class="truncate">{{ $item['label'] }}</span>

                                {{-- Tooltip when collapsed (desktop only) --}}
                                <span x-show="collapsed"
                                      x-cloak
                                      class="hidden lg:group-hover:flex absolute left-full ml-3 px-2.5 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-medium whitespace-nowrap shadow-lg z-50">
                                    {{ $item['label'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Collapse toggle (desktop only) --}}
        <div class="hidden lg:block border-t border-white/10 p-3">
            <button @click="collapsed = !collapsed"
                    class="w-full flex items-center justify-center gap-2 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors text-xs">
                <i class="fas" :class="collapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
                <span x-show="!collapsed">Collapse</span>
            </button>
        </div>
    </aside>

    {{-- Mobile backdrop --}}
    <div x-show="mobileOpen"
         x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 bg-slate-900/60 z-30 lg:hidden"
         x-transition.opacity>
    </div>
</div>
