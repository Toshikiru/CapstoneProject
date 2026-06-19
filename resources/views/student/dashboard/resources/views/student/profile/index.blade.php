@extends('layouts.student')
@section('title', 'My Profile')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">My Profile</h1>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Your personal information and exam records</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 text-center">
            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-blue-600 dark:text-blue-400 text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg">{{ $user->studentProfile?->full_name ?? $user->name }}</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-mono mt-0.5">{{ $user->student_id }}</p>

            @if($p = $user->studentProfile)
            <span class="mt-3 inline-block px-3 py-1 rounded-full text-xs font-semibold
                {{ $p->admission_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                   ($p->admission_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                   ($p->admission_status==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                   'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                {{ $p->admission_status ?? 'Pending' }}
            </span>
            @endif
        </div>
    </div>

    <div class="md:col-span-2 space-y-6">
        @if($p = $user->studentProfile)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4"><i class="fas fa-id-card mr-2 text-blue-500"></i>Personal Information</h3>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @foreach([
                    ['First Name',$p->first_name],['Middle Name',$p->middle_name??'—'],
                    ['Last Name',$p->last_name],['Suffix',$p->suffix??'—'],
                    ['Sex',$p->sex],['Date of Birth',$p->date_of_birth->format('M d, Y')],
                    ['Age',$p->age.' yrs old'],['Year Level',$p->year_level],
                    ['Course',$p->course],['Contact',$p->contact_number??'—'],
                ] as [$label,$value])
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-0.5">{{ $label }}</p>
                    <p class="font-medium text-slate-700 dark:text-slate-300">{{ $value }}</p>
                </div>
                @endforeach
                <div class="col-span-2">
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-0.5">Address</p>
                    <p class="font-medium text-slate-700 dark:text-slate-300">{{ $p->address }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4"><i class="fas fa-file-alt mr-2 text-indigo-500"></i>Exam Results</h3>
            @forelse($user->examSessions as $session)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <div>
                    <p class="font-medium text-slate-700 dark:text-slate-300">{{ $session->exam?->title }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $session->submitted_at?->format('M d, Y') ?? 'In progress' }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $session->percentage !== null ? round($session->percentage,1).'%' : '—' }}</p>
                    <span class="text-xs px-1.5 py-0.5 rounded font-medium
                        {{ $session->result_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                           ($session->result_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                           ($session->result_status==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                           'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                        {{ $session->result_status }}
                    </span>
                </div>
            </div>
            @empty
            <x-ui.empty-state icon="fa-file-alt" title="No exams taken yet" subtitle="Your results will appear here." />
            @endforelse
        </div>
    </div>
</div>
@endsection
