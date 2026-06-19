@extends('layouts.admin')
@section('title', 'Examinations')
@section('content')

<x-ui.page-header title="Examinations" subtitle="Manage entrance examination papers" :breadcrumbs="[['label'=>'Examinations']]">
    <a href="{{ route('admin.exams.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i> Create Exam
    </a>
</x-ui.page-header>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    @if($exams->count())
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Title</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden md:table-cell">Access Code</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden lg:table-cell">Questions</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden lg:table-cell">Time</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Status</th>
                <th class="text-right px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($exams as $exam)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ $exam->title }}</p>
                    <p class="text-xs text-slate-400">{{ $exam->examSessions->count() }} submission(s)</p>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="font-mono bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-xs font-bold text-blue-700 dark:text-blue-400">{{ $exam->access_code }}</span>
                </td>
                <td class="px-4 py-3 hidden lg:table-cell text-slate-600 dark:text-slate-400">{{ $exam->questions_count }}</td>
                <td class="px-4 py-3 hidden lg:table-cell text-slate-600 dark:text-slate-400">{{ $exam->time_limit }} min</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $exam->is_active ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                        {{ $exam->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.exams.show', $exam) }}" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.questions.index', $exam) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Questions"><i class="fas fa-list"></i></a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="p-1.5 text-slate-400 hover:text-yellow-600 dark:hover:text-yellow-400" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.exams.toggle-active', $exam) }}">@csrf @method('PATCH')
                            <button class="p-1.5 text-slate-400 hover:text-{{ $exam->is_active?'orange':'green' }}-600" title="{{ $exam->is_active?'Deactivate':'Activate' }}">
                                <i class="fas fa-{{ $exam->is_active?'pause':'play' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.exams.duplicate', $exam) }}">@csrf
                            <button class="p-1.5 text-slate-400 hover:text-teal-600 dark:hover:text-teal-400" title="Duplicate"><i class="fas fa-copy"></i></button>
                        </form>
                        <x-ui.confirm-modal
                            :action="route('admin.exams.destroy', $exam)"
                            method="DELETE"
                            title="Delete this exam?"
                            message="Deleting '{{ $exam->title }}' will also remove all questions, sections, and student results. This cannot be undone."
                            confirm-label="Delete Exam"
                            trigger-class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400">
                            <i class="fas fa-trash"></i>
                        </x-ui.confirm-modal>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">{{ $exams->links() }}</div>
    @else
        <x-ui.empty-state icon="fa-file-alt" title="No exams yet" subtitle="Create your first entrance examination to get started.">
            <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                <i class="fas fa-plus"></i> Create Exam
            </a>
        </x-ui.empty-state>
    @endif
</div>
@endsection
