@extends('layouts.admin')
@section('title', 'Result Detail')
@section('content')

<x-ui.page-header
    :title="$session->user->name"
    :subtitle="$session->exam->title"
    :back="route('admin.results.index')"
    :breadcrumbs="[['label'=>'Results','url'=>route('admin.results.index')],['label'=>$session->user->name]]"
>
    <a href="{{ route('admin.results.admission-slip', $session) }}" target="_blank"
       class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
        <i class="fas fa-print"></i> Print Admission Slip
    </a>
</x-ui.page-header>

{{-- Score Summary --}}
@php $r = $session->result_status; @endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Raw Score</p>
        <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $session->raw_score ?? '—' }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Percentage</p>
        <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $session->percentage ? round($session->percentage,1).'%' : '—' }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Interpretation</p>
        <p class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $session->interpretation ?? '—' }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Admission Status</p>
        <span class="inline-block px-3 py-1 rounded-full text-sm font-bold
            {{ $r==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
               ($r==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
               ($r==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
               'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
            {{ $r }}
        </span>
    </div>
</div>

{{-- Manual Grading --}}
@php $shortAnswers = $session->responses->filter(fn($resp) => $resp->question->type === 'short_answer'); @endphp
@if($shortAnswers->count() > 0 && !$session->is_graded)
<div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-6 mb-6">
    <h3 class="font-semibold text-amber-800 dark:text-amber-300 mb-4"><i class="fas fa-edit mr-2"></i>Manual Grading Required</h3>
    <form method="POST" action="{{ route('admin.results.grade', $session) }}" class="space-y-3">
        @csrf
        @foreach($shortAnswers as $i => $resp)
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-amber-100 dark:border-amber-500/20">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $resp->question->question_text }}</p>
            <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-3 mb-3">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Student's Answer:</p>
                <p class="text-sm text-slate-800 dark:text-slate-200">{{ $resp->answer ?? '(No answer provided)' }}</p>
            </div>
            <input type="hidden" name="responses[{{ $i }}][id]" value="{{ $resp->id }}">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Score (max {{ $resp->question->points }})</label>
                    <input type="number" name="responses[{{ $i }}][score]" value="{{ $resp->score ?? 0 }}"
                           min="0" max="{{ $resp->question->points }}" step="0.5"
                           class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Remarks</label>
                    <input type="text" name="responses[{{ $i }}][remarks]" value="{{ $resp->grader_remarks ?? '' }}"
                           class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
        </div>
        @endforeach
        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
            <i class="fas fa-save mr-2"></i>Save Grades &amp; Recalculate
        </button>
    </form>
</div>
@endif

{{-- All Responses --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">All Responses</h3>
    <div class="space-y-3">
        @foreach($session->responses as $i => $resp)
        <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                        <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded uppercase tracking-wide">{{ str_replace('_',' ',$resp->question->type) }}</span>
                        <span class="text-xs text-slate-400">Q{{ $i+1 }}</span>
                    </div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $resp->question->question_text }}</p>
                    <div class="flex items-center gap-4 flex-wrap">
                        <div>
                            <span class="text-xs text-slate-400">Answer: </span>
                            <span class="text-sm font-medium {{ $resp->is_correct===true?'text-green-700 dark:text-green-400':($resp->is_correct===false?'text-red-600 dark:text-red-400':'text-slate-700 dark:text-slate-300') }}">
                                {{ $resp->answer ?? '—' }}
                            </span>
                        </div>
                        @if($resp->question->correct_answer)
                        <div>
                            <span class="text-xs text-slate-400">Correct: </span>
                            <span class="text-sm text-green-700 dark:text-green-400 font-medium">{{ $resp->question->correct_answer }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($resp->is_correct !== null)
                    <span class="{{ $resp->is_correct?'text-green-500':'text-red-500' }} text-xl">
                        <i class="fas fa-{{ $resp->is_correct?'check':'times' }}-circle"></i>
                    </span>
                    @endif
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 mt-1">{{ $resp->score ?? '—' }}/{{ $resp->question->points }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
