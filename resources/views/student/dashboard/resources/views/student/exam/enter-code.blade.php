@extends('layouts.student')
@section('title', 'Enter Exam Code')
@section('content')

<div class="max-w-lg mx-auto mt-12">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-8">
        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-key text-blue-600 dark:text-blue-400 text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 text-center mb-1">Enter Exam Code</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm text-center mb-6">Enter the access code provided by your Guidance Counselor.</p>

        @if(isset($exam))
        <div class="mb-5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl p-4">
            <h3 class="font-semibold text-blue-800 dark:text-blue-300">{{ $exam->title }}</h3>
            <div class="mt-2 space-y-1 text-sm text-blue-700 dark:text-blue-300">
                <p><i class="fas fa-clock mr-2"></i>Time Limit: {{ $exam->time_limit }} minutes</p>
                <p><i class="fas fa-list mr-2"></i>Questions: {{ $exam->questions_count }}</p>
                @if($exam->instructions)
                <p class="mt-2 text-blue-600 dark:text-blue-400">{{ $exam->instructions }}</p>
                @endif
            </div>
            <form method="POST" action="{{ route('student.exam.start', $exam) }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-play mr-2"></i>Start Exam Now
                </button>
            </form>
        </div>
        @else
        <form method="POST" action="{{ route('student.exam.enter') }}">
            @csrf
            <input type="text" name="access_code" placeholder="e.g. TPC2024A" autofocus maxlength="20"
                   class="w-full border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-4 py-3 text-center text-lg font-mono uppercase tracking-widest mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>Submit Code
            </button>
        </form>
        @endif

        <div class="mt-4 text-center">
            <a href="{{ route('student.dashboard') }}" class="text-sm text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">
                <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
