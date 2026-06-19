@extends('layouts.admin')
@section('title', 'Exam Details')
@section('content')

<x-ui.page-header
    :title="$exam->title"
    :back="route('admin.exams.index')"
    :breadcrumbs="[['label'=>'Examinations','url'=>route('admin.exams.index')],['label'=>$exam->title]]"
>
    <a href="{{ route('admin.questions.index', $exam) }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
        <i class="fas fa-list"></i> Questions
    </a>
    <form method="POST" action="{{ route('admin.exams.toggle-active', $exam) }}">@csrf @method('PATCH')
        <button class="px-4 py-2.5 rounded-xl text-sm font-medium border transition-colors
            {{ $exam->is_active
                ? 'border-orange-200 text-orange-600 hover:bg-orange-50 dark:border-orange-500/30 dark:text-orange-400 dark:hover:bg-orange-500/10'
                : 'border-green-200 text-green-600 hover:bg-green-50 dark:border-green-500/30 dark:text-green-400 dark:hover:bg-green-500/10' }}">
            {{ $exam->is_active ? 'Deactivate' : 'Activate' }}
        </button>
    </form>
    <form method="POST" action="{{ route('admin.exams.regenerate-code', $exam) }}">@csrf
        <button class="px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fas fa-sync mr-1"></i>New Code
        </button>
    </form>
    <a href="{{ route('admin.exams.edit', $exam) }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
        <i class="fas fa-edit"></i> Edit
    </a>
</x-ui.page-header>

{{-- Access Code Banner --}}
<div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-2xl px-5 py-3 mb-6 flex items-center gap-4 flex-wrap">
    <span class="text-sm text-blue-700 dark:text-blue-300">Access Code:</span>
    <span class="font-mono text-2xl font-black text-blue-700 dark:text-blue-300 tracking-widest">{{ $exam->access_code }}</span>
    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $exam->is_active ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
        {{ $exam->is_active ? 'Active' : 'Inactive' }}
    </span>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Questions', $exam->total_questions, 'fa-question-circle', 'blue'],
        ['Total Points',    $exam->total_points,    'fa-star',            'yellow'],
        ['Time Limit',      $exam->time_limit.' min','fa-clock',           'teal'],
        ['Submissions',     $exam->examSessions->count(), 'fa-paper-plane','indigo'],
    ] as [$label, $val, $icon, $c])
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1"><i class="fas {{ $icon }} mr-1 text-{{ $c }}-500"></i>{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $val }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sections & Questions --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Sections &amp; Questions</h3>
        @forelse($exam->sections as $section)
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded text-xs font-bold">Section {{ $loop->iteration }}</span>
                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $section->title }}</span>
                <span class="text-xs text-slate-400 dark:text-slate-500">({{ $section->questions->count() }} questions)</span>
            </div>
            <div class="ml-4 space-y-0">
                @foreach($section->questions as $q)
                <div class="py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 flex items-start gap-2">
                    <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded mt-0.5 uppercase flex-shrink-0">{{ str_replace('_',' ',$q->type) }}</span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 flex-1">{{ Str::limit($q->question_text, 80) }}</p>
                    <span class="text-xs text-slate-400 dark:text-slate-500 flex-shrink-0">{{ $q->points }}pt</span>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <x-ui.empty-state icon="fa-list" title="No sections yet" subtitle="Add sections and questions to this exam.">
            <a href="{{ route('admin.questions.index', $exam) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                <i class="fas fa-plus"></i> Add Questions
            </a>
        </x-ui.empty-state>
        @endforelse
    </div>

    <div class="space-y-4">
        {{-- Score Interpretation --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6" x-data="{ editing: false }">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200">Score Interpretation</h3>
                <button @click="editing = !editing" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium">
                    <i class="fas fa-edit mr-1"></i><span x-text="editing ? 'Cancel' : 'Edit'"></span>
                </button>
            </div>

            <div x-show="!editing">
                @foreach($exam->scoreInterpretations->sortByDesc('min_score') as $interp)
                <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 gap-2">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $interp->label }}</span>
                    <span class="text-xs text-slate-400">{{ $interp->min_score }}–{{ $interp->max_score }}%</span>
                    <span class="text-xs px-1.5 py-0.5 rounded
                        {{ $interp->admission_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                           ($interp->admission_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                           'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400') }}">
                        {{ $interp->admission_status }}
                    </span>
                </div>
                @endforeach
            </div>

            <div x-show="editing" x-cloak>
                <form method="POST" action="{{ route('admin.exams.interpretations.update', $exam) }}">
                    @csrf @method('PUT')
                    @if($errors->has('interpretations'))
                    <p class="text-red-500 text-xs mb-3">{{ $errors->first('interpretations') }}</p>
                    @endif
                    @foreach($exam->scoreInterpretations->sortByDesc('min_score') as $i => $interp)
                    <input type="hidden" name="interpretations[{{ $i }}][id]" value="{{ $interp->id }}">
                    <div class="mb-2 space-y-1.5">
                        <input type="text" name="interpretations[{{ $i }}][label]" value="{{ $interp->label }}"
                               placeholder="Label" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <div class="flex items-center gap-1">
                            <input type="number" name="interpretations[{{ $i }}][min_score]" value="{{ $interp->min_score }}" min="0" max="100" step="0.01"
                                   placeholder="Min" class="flex-1 border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span class="text-slate-400 text-xs">–</span>
                            <input type="number" name="interpretations[{{ $i }}][max_score]" value="{{ $interp->max_score }}" min="0" max="100" step="0.01"
                                   placeholder="Max" class="flex-1 border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <select name="interpretations[{{ $i }}][admission_status]" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @foreach(['Passed','Conditional','Failed'] as $s)
                            <option value="{{ $s }}" {{ $interp->admission_status===$s?'selected':'' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-xs font-medium mt-2 transition-colors">
                        <i class="fas fa-save mr-1"></i>Save Thresholds
                    </button>
                </form>
            </div>
        </div>

        {{-- Settings --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Settings</h3>
            <div class="space-y-2.5 text-sm">
                @foreach([
                    ['Passing Score',   $exam->passing_score.'%'],
                    ['Max Attempts',    $exam->max_attempts],
                    ['Available From',  $exam->available_from?->format('M d, Y') ?? 'Any time'],
                    ['Available Until', $exam->available_until?->format('M d, Y') ?? 'No limit'],
                ] as [$label, $val])
                <div class="flex justify-between gap-2">
                    <span class="text-slate-500 dark:text-slate-400">{{ $label }}</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300 text-right">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
