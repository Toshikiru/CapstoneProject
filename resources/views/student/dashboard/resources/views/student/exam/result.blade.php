@extends('layouts.student')
@section('title', 'Exam Result')
@section('content')

<div class="max-w-2xl mx-auto mt-8">
    @php
        $passed = $session->result_status === 'Passed';
        $conditional = $session->result_status === 'Conditional';
        $failed = $session->result_status === 'Failed';
        $pending = $session->result_status === 'Pending';
    @endphp

    {{-- Result Hero --}}
    <div class="text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-10 mb-6">
        <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-4
            {{ $passed?'bg-green-100 dark:bg-green-500/10':($conditional?'bg-yellow-100 dark:bg-yellow-500/10':($failed?'bg-red-100 dark:bg-red-500/10':'bg-slate-100 dark:bg-slate-700')) }}">
            <i class="fas text-4xl {{ $passed?'fa-check-circle text-green-600 dark:text-green-400':($conditional?'fa-exclamation-circle text-yellow-600 dark:text-yellow-400':($failed?'fa-times-circle text-red-600 dark:text-red-400':'fa-hourglass-half text-slate-400')) }}"></i>
        </div>
        <h2 class="text-3xl font-bold {{ $passed?'text-green-700 dark:text-green-400':($conditional?'text-yellow-700 dark:text-yellow-400':($failed?'text-red-700 dark:text-red-400':'text-slate-600 dark:text-slate-400')) }} mb-1">
            {{ $pending ? 'Awaiting Grading' : $session->result_status }}
        </h2>
        @if($session->interpretation)
        <p class="text-slate-500 dark:text-slate-400 mt-1">{{ $session->interpretation }}</p>
        @endif
        @if($session->percentage !== null)
        <p class="text-5xl font-black text-slate-800 dark:text-slate-100 mt-4">{{ round($session->percentage, 1) }}<span class="text-2xl font-bold text-slate-400">%</span></p>
        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Raw Score: {{ $session->raw_score }}</p>
        @endif
    </div>

    {{-- Details --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Exam Details</h3>
        <div class="space-y-2.5 text-sm">
            @foreach([
                ['label'=>'Exam','value'=>$session->exam?->title],
                ['label'=>'Student','value'=>$session->user?->name],
                ['label'=>'Student ID','value'=>$session->user?->student_id],
                ['label'=>'Started','value'=>$session->started_at?->format('M d, Y h:i A')],
                ['label'=>'Submitted','value'=>$session->submitted_at?->format('M d, Y h:i A')],
            ] as $row)
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">{{ $row['label'] }}</span>
                <span class="font-medium text-slate-700 dark:text-slate-300 text-right">{{ $row['value'] ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('student.dashboard') }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium transition-colors">
            <i class="fas fa-home mr-2"></i>Back to Dashboard
        </a>
        @if($session->is_graded)
        <a href="{{ route('admin.results.admission-slip', $session) }}" target="_blank"
           class="flex-1 text-center border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 py-3 rounded-xl font-medium transition-colors">
            <i class="fas fa-print mr-2"></i>Print Admission Slip
        </a>
        @endif
    </div>
</div>
@endsection
