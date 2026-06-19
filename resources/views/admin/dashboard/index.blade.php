@extends('layouts.admin')
@section('title', 'Dashboard')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
@endpush

@section('content')

<x-ui.page-header title="Dashboard" subtitle="Overview of entrance examination activities" />

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-ui.stat-card label="Total Students" :value="$stats['total_students']" icon="fa-users" color="blue" />
    <x-ui.stat-card label="Total Examinees" :value="$stats['total_examinees']" icon="fa-file-alt" color="indigo" />
    <x-ui.stat-card label="Passed" :value="$stats['passed']" icon="fa-check-circle" color="green" />
    <x-ui.stat-card label="Conditional" :value="$stats['conditional']" icon="fa-exclamation-circle" color="yellow" />
    <x-ui.stat-card label="Failed" :value="$stats['failed']" icon="fa-times-circle" color="red" />
    <x-ui.stat-card label="Pending Grading" :value="$stats['pending_grading']" icon="fa-clock" color="orange" />
    <x-ui.stat-card label="Active Exams" :value="$stats['active_exams']" icon="fa-play-circle" color="teal" />
    <x-ui.stat-card label="Currently Taking" :value="$stats['in_progress']" icon="fa-spinner" color="purple" />
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Pass/Fail Distribution --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">
            <i class="fas fa-chart-pie mr-2 text-blue-500"></i>Pass / Fail Distribution
        </h3>
        @if($stats['passed'] + $stats['conditional'] + $stats['failed'] > 0)
            <div class="relative h-56">
                <canvas id="passFailChart"></canvas>
            </div>
        @else
            <x-ui.empty-state icon="fa-chart-pie" title="No results yet" subtitle="Results will appear here once students complete exams." />
        @endif
    </div>

    {{-- Daily Examination Activity --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">
            <i class="fas fa-chart-line mr-2 text-indigo-500"></i>Daily Examination Activity
            <span class="text-xs font-normal text-slate-400">(last 14 days)</span>
        </h3>
        @if(array_sum($dailyCounts) > 0)
            <div class="relative h-56">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        @else
            <x-ui.empty-state icon="fa-chart-line" title="No activity yet" subtitle="Daily exam submissions will be charted here." />
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Course Distribution --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">
            <i class="fas fa-book-open mr-2 text-blue-500"></i>Course Distribution
        </h3>
        @forelse($courseDistribution as $row)
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-slate-600 dark:text-slate-400 truncate flex-1 mr-3">{{ $row->course }}</span>
            <div class="flex items-center gap-2">
                <div class="w-24 bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($row->count / max($courseDistribution->max('count'), 1)) * 100 }}%"></div>
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 w-6 text-right">{{ $row->count }}</span>
            </div>
        </div>
        @empty
            <x-ui.empty-state icon="fa-book-open" title="No students yet" subtitle="Course distribution will appear once students are added." />
        @endforelse
    </div>

    {{-- Recent Activities --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">
            <i class="fas fa-history mr-2 text-indigo-500"></i>Recent Activities
        </h3>
        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($recentActivities as $log)
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-user text-slate-400 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $log->description }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $log->created_at->diffForHumans() }} — {{ $log->ip_address }}</p>
                </div>
            </div>
            @empty
                <x-ui.empty-state icon="fa-history" title="No activity yet" subtitle="System actions will be logged here." />
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.students.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-user-plus"></i> Add Student
        </a>
        <a href="{{ route('admin.exams.create') }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-plus"></i> Create Exam
        </a>
        <a href="{{ route('admin.monitoring.index') }}" class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-desktop"></i> Live Monitor
        </a>
        <a href="{{ route('admin.results.index') }}" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-chart-bar"></i> View Results
        </a>
        <a href="{{ route('admin.backup.index') }}" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-database"></i> Backup
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(100,116,139,0.08)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    @if($stats['passed'] + $stats['conditional'] + $stats['failed'] > 0)
    new Chart(document.getElementById('passFailChart'), {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Conditional', 'Failed'],
            datasets: [{
                data: [{{ $stats['passed'] }}, {{ $stats['conditional'] }}, {{ $stats['failed'] }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { size: 12 }, padding: 12 } }
            },
            cutout: '65%',
        }
    });
    @endif

    @if(array_sum($dailyCounts) > 0)
    new Chart(document.getElementById('dailyActivityChart'), {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Submissions',
                data: @json($dailyCounts),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: '#3b82f6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1, color: textColor, font: { size: 11 } }, grid: { color: gridColor } }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection
