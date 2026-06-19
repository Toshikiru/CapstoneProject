@extends('layouts.admin')
@section('title', 'Live Monitoring')
@section('content')

<x-ui.page-header title="Live Monitoring" subtitle="Real-time view of active exam sessions" :breadcrumbs="[['label'=>'Live Monitoring']]">
    <span class="flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400">
        <span class="w-2 h-2 bg-emerald-500 rounded-full pulse-live"></span> Live
    </span>
</x-ui.page-header>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
     x-data="{ sessions: @json($sessions), loading: false }"
     x-init="setInterval(async () => {
         loading = true;
         const r = await fetch('{{ route('admin.monitoring.live') }}', { headers: {'X-Requested-With':'XMLHttpRequest'} });
         const d = await r.json();
         sessions = d.sessions;
         loading = false;
     }, 5000)">

    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <span class="text-sm text-slate-600 dark:text-slate-400">
            <span x-text="sessions.length"></span> active session(s)
        </span>
        <span x-show="loading" class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
            <i class="fas fa-sync fa-spin text-xs"></i> Updating...
        </span>
    </div>

    <template x-if="sessions.length === 0">
        <div class="py-16 text-center text-slate-400 dark:text-slate-600">
            <i class="fas fa-display text-4xl mb-3 block"></i>
            <p class="font-medium text-slate-600 dark:text-slate-400">No active sessions</p>
            <p class="text-sm mt-1">This refreshes every 5 seconds</p>
        </div>
    </template>

    <template x-if="sessions.length > 0">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Student</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden md:table-cell">Exam</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Progress</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Time Left</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden lg:table-cell">Focus Loss</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                <template x-for="s in sessions" :key="s.id">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800 dark:text-slate-200" x-text="s.student_name"></p>
                            <p class="text-xs text-slate-400 font-mono" x-text="s.student_id"></p>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 hidden md:table-cell" x-text="s.exam_title"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-2 w-24">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" :style="'width:'+s.progress+'%'"></div>
                                </div>
                                <span class="text-xs text-slate-600 dark:text-slate-400" x-text="s.progress+'%'"></span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-bold"
                                  :class="s.time_remaining < 300 ? 'text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-slate-300'"
                                  x-text="Math.floor(s.time_remaining/60)+':'+String(s.time_remaining%60).padStart(2,'0')"></span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <span x-text="s.focus_loss_count"
                                  :class="s.focus_loss_count > 3 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-slate-600 dark:text-slate-400'"></span>
                            <span class="text-slate-400 dark:text-slate-500 text-xs"> event(s)</span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        </div>
    </template>
</div>
@endsection
