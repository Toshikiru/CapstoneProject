@extends('layouts.admin')
@section('title', 'Question Builder')
@section('content')

<x-ui.page-header
    :title="'Question Builder: '.$exam->title"
    :back="route('admin.exams.show', $exam)"
    :breadcrumbs="[['label'=>'Examinations','url'=>route('admin.exams.index')],['label'=>$exam->title,'url'=>route('admin.exams.show',$exam)],['label'=>'Questions']]"
/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Forms --}}
    <div class="space-y-4">

        {{-- Add Section --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5" x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex items-center justify-between text-sm font-semibold text-slate-700 dark:text-slate-200">
                <span><i class="fas fa-folder-plus mr-2 text-blue-500"></i>Add Section</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fas text-slate-400 text-xs"></i>
            </button>
            <div x-show="open" x-cloak class="mt-4 space-y-3">
                <form method="POST" action="{{ route('admin.sections.store', $exam) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Section Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Instructions</label>
                        <textarea name="instructions" rows="2" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-sm font-medium transition-colors">Add Section</button>
                </form>
            </div>
        </div>

        {{-- Add Question --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5" x-data="{ type: 'multiple_choice' }">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4"><i class="fas fa-plus-circle mr-2 text-indigo-500"></i>Add Question</h3>
            <form method="POST" action="{{ route('admin.questions.store', $exam) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Section <span class="text-red-500">*</span></label>
                    <select name="section_id" required class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Select section</option>
                        @foreach($exam->sections as $s)<option value="{{ $s->id }}">{{ $s->title }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" x-model="type" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_or_false">True or False</option>
                        <option value="likert_scale">Likert Scale (1–5)</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Question <span class="text-red-500">*</span></label>
                    <textarea name="question_text" rows="3" required class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>

                <div x-show="type === 'multiple_choice'">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Answer Choices</label>
                    @for($i=0;$i<4;$i++)
                    <input type="text" name="options[]" placeholder="Option {{ $i+1 }}" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-1.5 text-sm mb-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @endfor
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mt-2 mb-1">Correct Answer</label>
                    <input type="text" name="correct_answer" placeholder="Type the exact correct option" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div x-show="type === 'true_or_false'">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Correct Answer</label>
                    <select name="correct_answer" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="True">True</option>
                        <option value="False">False</option>
                    </select>
                </div>

                <div x-show="type === 'likert_scale'">
                    <p class="text-xs text-slate-500 dark:text-slate-400 bg-blue-50 dark:bg-blue-500/10 rounded-lg p-2.5">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        Standard 1–5 scale (Strongly Disagree → Strongly Agree) is auto-generated. Points = max score for "Strongly Agree."
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Points <span class="text-red-500">*</span></label>
                    <input type="number" name="points" value="1" min="0" step="0.5" required class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-xl text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-1"></i>Add Question
                </button>
            </form>
        </div>
    </div>

    {{-- Right: Questions List --}}
    <div class="lg:col-span-2 space-y-4">
        @forelse($exam->sections as $section)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 px-5 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $section->title }}</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500 ml-2">{{ $section->questions->count() }} question(s)</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($section->questions as $i => $q)
                <div class="px-5 py-3.5 flex items-start gap-3">
                    <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-1.5 py-1 rounded mt-0.5 flex-shrink-0 uppercase tracking-wide">{{ str_replace('_',' ',$q->type) }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $i+1 }}. {{ $q->question_text }}</p>
                        @if($q->options)
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            @foreach($q->options as $opt)
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $opt===$q->correct_answer ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400 font-medium' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                {{ $opt }}{{ $opt===$q->correct_answer ? ' ✓' : '' }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                        <span class="text-xs text-slate-400 dark:text-slate-500 mt-1 block">{{ $q->points }} pt(s)</span>
                    </div>
                    <x-ui.confirm-modal
                        :action="route('admin.questions.destroy', [$exam, $q])"
                        method="DELETE"
                        title="Delete question?"
                        message="This question and any student responses to it will be permanently deleted."
                        confirm-label="Delete"
                        trigger-class="text-slate-300 hover:text-red-500 dark:hover:text-red-400 p-1 flex-shrink-0 mt-0.5">
                        <i class="fas fa-trash text-xs"></i>
                    </x-ui.confirm-modal>
                </div>
                @empty
                <p class="text-center py-8 text-slate-400 dark:text-slate-600 text-sm">No questions in this section yet.</p>
                @endforelse
            </div>
        </div>
        @empty
        <x-ui.empty-state icon="fa-folder-open" title="No sections yet" subtitle="Add a section first using the panel on the left, then add questions to it." />
        @endforelse
    </div>
</div>
@endsection
