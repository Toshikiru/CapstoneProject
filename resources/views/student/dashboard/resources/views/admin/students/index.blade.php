@extends('layouts.admin')
@section('title', 'Students')
@section('content')

<x-ui.page-header title="Students" subtitle="Manage student accounts and profiles" :breadcrumbs="[['label' => 'Students']]">
    <a href="{{ route('admin.students.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-user-plus"></i> Add Student
    </a>
</x-ui.page-header>

{{-- Filters --}}
<form method="GET" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or ID..."
           class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-blue-400">
    <select name="course" class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Courses</option>
        @foreach($courses as $c)<option value="{{ $c }}" {{ request('course')===$c?'selected':'' }}>{{ $c }}</option>@endforeach
    </select>
    <select name="year_level" class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Year Levels</option>
        @foreach($yearLevels as $y)<option value="{{ $y }}" {{ request('year_level')===$y?'selected':'' }}>{{ $y }}</option>@endforeach
    </select>
    <select name="admission_status" class="border border-slate-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Status</option>
        @foreach(['Pending','Passed','Conditional','Failed'] as $s)<option value="{{ $s }}" {{ request('admission_status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
    </select>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Filter</button>
    <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-xl text-sm border border-slate-200 dark:border-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Reset</a>
</form>

{{-- Table --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    @if($students->count())
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Student ID</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Name</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden md:table-cell">Course</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 hidden lg:table-cell">Year</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Account</th>
                <th class="text-right px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($students as $student)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                <td class="px-4 py-3 font-mono text-blue-700 dark:text-blue-400 font-medium">{{ $student->student_id }}</td>
                <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $student->name }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 hidden md:table-cell truncate max-w-48">{{ $student->studentProfile?->course ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-slate-400 hidden lg:table-cell">{{ $student->studentProfile?->year_level ?? '—' }}</td>
                <td class="px-4 py-3">
                    @php $s = $student->studentProfile?->admission_status ?? 'Pending'; @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $s==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':($s==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':($s==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                        {{ $s }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $student->is_active?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' }}">
                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.students.show', $student) }}" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.students.toggle-active', $student) }}">@csrf @method('PATCH')
                            <button class="p-1.5 text-slate-400 hover:text-{{ $student->is_active?'orange':'green' }}-600" title="{{ $student->is_active?'Deactivate':'Activate' }}">
                                <i class="fas fa-{{ $student->is_active?'ban':'check' }}"></i>
                            </button>
                        </form>
                        <x-ui.confirm-modal
                            :action="route('admin.students.destroy', $student)"
                            method="DELETE"
                            title="Delete this student?"
                            message="This will permanently remove {{ $student->name }} and all related exam records, results, and profile data. This cannot be undone."
                            confirm-label="Delete Student"
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
    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">{{ $students->links() }}</div>
    @else
        <x-ui.empty-state icon="fa-users" title="No students found" subtitle="Try adjusting your filters, or add a new student to get started.">
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                <i class="fas fa-user-plus"></i> Add Student
            </a>
        </x-ui.empty-state>
    @endif
</div>
@endsection
