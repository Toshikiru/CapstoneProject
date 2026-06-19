{{--
    Dashboard stat card.
    Usage: <x-ui.stat-card label="Total Students" :value="42" icon="fa-users" color="blue" />
    Optional: trend="+12%" trend_up="true"
--}}
@props(['label', 'value', 'icon', 'color' => 'blue', 'trend' => null, 'trendUp' => true])

@php
$palette = [
    'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'ring' => 'ring-blue-100 dark:ring-blue-500/20'],
    'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-500/10', 'text' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-100 dark:ring-indigo-500/20'],
    'green'  => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'ring-emerald-100 dark:ring-emerald-500/20'],
    'yellow' => ['bg' => 'bg-amber-50 dark:bg-amber-500/10', 'text' => 'text-amber-600 dark:text-amber-400', 'ring' => 'ring-amber-100 dark:ring-amber-500/20'],
    'red'    => ['bg' => 'bg-red-50 dark:bg-red-500/10', 'text' => 'text-red-600 dark:text-red-400', 'ring' => 'ring-red-100 dark:ring-red-500/20'],
    'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-500/10', 'text' => 'text-orange-600 dark:text-orange-400', 'ring' => 'ring-orange-100 dark:ring-orange-500/20'],
    'teal'   => ['bg' => 'bg-teal-50 dark:bg-teal-500/10', 'text' => 'text-teal-600 dark:text-teal-400', 'ring' => 'ring-teal-100 dark:ring-teal-500/20'],
    'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-500/10', 'text' => 'text-purple-600 dark:text-purple-400', 'ring' => 'ring-purple-100 dark:ring-purple-500/20'],
];
$p = $palette[$color] ?? $palette['blue'];
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">{{ $label }}</p>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 tabular-nums">{{ $value }}</p>
            @if($trend)
                <p class="text-xs mt-2 flex items-center gap-1 {{ $trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                    <i class="fas fa-arrow-{{ $trendUp ? 'up' : 'down' }} text-[10px]"></i>
                    {{ $trend }}
                </p>
            @endif
        </div>
        <div class="w-11 h-11 rounded-xl {{ $p['bg'] }} {{ $p['text'] }} ring-1 {{ $p['ring'] }} flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $icon }} text-lg"></i>
        </div>
    </div>
</div>
