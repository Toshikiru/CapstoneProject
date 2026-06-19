@extends('layouts.admin')
@section('title', 'Results')
@section('content')

<x-ui.page-header title="Exam Results" subtitle="View and manage student examination results" :breadcrumbs="[['label'=>'Results']]"/>

{{-- Filters --}}
<form method="GET" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name or ID..."
           class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-blue-400">
    <select name="exam_id" class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Exams</option>
        @foreach($exams as $exam)<option value="{{ $exam->id }}" {{ request('exam_id')==$exam->id?'selected':'' }}>{{ $exam->title }}</option>@endforeach
    </select>
    <select name="result_status" class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Results</option>
        @foreach(['Passed','Conditional','Failed','Pending'] as $s)<option value="{{ $s }}" {{ request('result_status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
    </select>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Filter</button>
    <a href="{{ route('admin.results.index') }}" class="px-4 py-2 rounded-xl text-sm border border-slate-200 dark:border-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Reset</a>
</form>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    @if($sessions->count())
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Student</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden md:table-cell">Exam</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden lg:table-cell">Submitted</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Score</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Result</th>
                <th class="text-right px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($sessions as $session)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                <td class="px-4 py-3">
                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ $session->user?->name }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $session->user?->student_id }}</p>
                </td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 hidden md:table-cell">{{ $session->exam?->title }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 hidden lg:table-cell">
                    {{ $session->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                </td>
                <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                    {{ $session->percentage !== null ? round($session->percentage,1).'%' : '—' }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $session->result_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                           ($session->result_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                           ($session->result_status==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                           'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400')) }}">
                        {{ $session->result_status }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.results.show', $session) }}" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400" title="View"><i class="fas fa-eye"></i></a>
                    @if(!$session->is_graded)
                    <a href="{{ route('admin.results.show', $session) }}" class="ml-1 text-xs text-amber-600 dark:text-amber-400 font-medium hover:underline">Grade</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">{{ $sessions->links() }}</div>
    @else
        <x-ui.empty-state icon="fa-chart-bar" title="No results yet" subtitle="Results will appear here once students complete and submit exams." />
    @endif
</div>
@endsection
