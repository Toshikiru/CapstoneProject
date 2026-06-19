@extends('layouts.student')
@section('title', 'Student Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Welcome, {{ auth()->user()->name }}!</h1>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">{{ auth()->user()->student_id }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Exams Taken</p>
        <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['exams_taken'] }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Admission Status</p>
        @php $s = $stats['admission_status'] ?? 'Pending'; @endphp
        <p class="text-2xl font-bold {{ $s==='Passed'?'text-emerald-600 dark:text-emerald-400':($s==='Conditional'?'text-amber-600 dark:text-amber-400':($s==='Failed'?'text-red-600 dark:text-red-400':'text-slate-600 dark:text-slate-400')) }}">{{ $s }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Best Score</p>
        <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['best_score'] !== null ? round($stats['best_score'],1).'%' : '—' }}</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-6">
    <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-5"><i class="fas fa-play-circle mr-2 text-blue-500"></i>Enter Exam</h2>
    <form method="POST" action="{{ route('student.exam.start') }}" class="flex gap-3 flex-wrap">
        @csrf
        <input type="text" name="access_code" placeholder="Enter access code (e.g. TPC2024A)" maxlength="20"
               class="flex-1 min-w-48 border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono uppercase tracking-wide focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium text-sm transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>Start Exam
        </button>
    </form>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200"><i class="fas fa-history mr-2 text-indigo-500"></i>Exam History</h2>
    </div>
    @forelse($recentSessions as $session)
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50 dark:border-slate-700/50 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
        <div>
            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $session->exam?->title }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $session->submitted_at?->format('M d, Y') ?? 'In progress' }}</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-lg text-slate-800 dark:text-slate-200">{{ $session->percentage !== null ? round($session->percentage,1).'%' : '—' }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                {{ $session->result_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                   ($session->result_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                   ($session->result_status==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                   'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                {{ $session->result_status }}
            </span>
        </div>
    </div>
    @empty
    <div class="py-12 text-center text-slate-400 dark:text-slate-600">
        <i class="fas fa-file-alt text-3xl mb-2 block"></i>
        <p class="font-medium">No exams taken yet</p>
        <p class="text-sm mt-1">Enter an access code above to start.</p>
    </div>
    @endforelse
</div>
@endsection
