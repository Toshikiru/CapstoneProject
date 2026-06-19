@extends('layouts.admin')
@section('title', 'Student Profile')
@section('content')

<x-ui.page-header
    :title="$student->studentProfile?->full_name ?? $student->name"
    :subtitle="$student->student_id"
    :back="route('admin.students.index')"
    :breadcrumbs="[['label'=>'Students','url'=>route('admin.students.index')],['label'=>$student->studentProfile?->full_name ?? $student->name]]"
>
    <a href="{{ route('admin.students.edit', $student) }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
        <i class="fas fa-edit"></i> Edit
    </a>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column --}}
    <div class="space-y-4">
        {{-- Profile Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <div class="text-center mb-5">
                <div class="w-20 h-20 bg-blue-100 dark:bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-blue-600 dark:text-blue-400 text-3xl font-bold">{{ substr($student->name, 0, 1) }}</span>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg">{{ $student->studentProfile?->full_name ?? $student->name }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $student->studentProfile?->course ?? 'No course set' }}</p>
                @php $s = $student->studentProfile?->admission_status ?? 'Pending'; @endphp
                <span class="mt-2 inline-block px-3 py-1 rounded-full text-xs font-semibold
                    {{ $s==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                       ($s==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                       ($s==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                       'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                    {{ $s }}
                </span>
            </div>

            @if($p = $student->studentProfile)
            <div class="space-y-2.5 text-sm border-t border-slate-100 dark:border-slate-700 pt-4">
                @foreach([
                    ['label'=>'Sex','value'=>$p->sex],
                    ['label'=>'Date of Birth','value'=>$p->date_of_birth->format('M d, Y')],
                    ['label'=>'Age','value'=>$p->age.' years old'],
                    ['label'=>'Year Level','value'=>$p->year_level],
                    ['label'=>'Contact','value'=>$p->contact_number ?? '—'],
                    ['label'=>'Guardian','value'=>$p->guardian_name ?? '—'],
                    ['label'=>'Guardian Contact','value'=>$p->guardian_contact_number ?? '—'],
                ] as $row)
                <div class="flex justify-between gap-2">
                    <span class="text-slate-500 dark:text-slate-400 flex-shrink-0">{{ $row['label'] }}</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300 text-right">{{ $row['value'] }}</span>
                </div>
                @endforeach
                @if($p->address)
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Address</span>
                    <p class="font-medium text-sm mt-0.5 text-slate-700 dark:text-slate-300">{{ $p->address }}</p>
                </div>
                @endif
            </div>
            @else
            <x-ui.empty-state icon="fa-id-card" title="No profile yet" subtitle="Profile info will appear after editing this student." />
            @endif

            <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700 space-y-2">
                <form method="POST" action="{{ route('admin.students.toggle-active', $student) }}">@csrf @method('PATCH')
                    <button class="w-full py-2 rounded-xl text-sm font-medium border transition-colors
                        {{ $student->is_active
                            ? 'border-orange-200 text-orange-600 hover:bg-orange-50 dark:border-orange-500/30 dark:text-orange-400 dark:hover:bg-orange-500/10'
                            : 'border-green-200 text-green-600 hover:bg-green-50 dark:border-green-500/30 dark:text-green-400 dark:hover:bg-green-500/10' }}">
                        <i class="fas fa-{{ $student->is_active ? 'ban' : 'check' }} mr-1"></i>
                        {{ $student->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>

                <div x-data="{ open: false }">
                    <button @click="open=!open" class="w-full py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-key mr-1"></i>Reset Password
                    </button>
                    <div x-show="open" x-cloak class="mt-3 space-y-2">
                        <form method="POST" action="{{ route('admin.students.reset-password', $student) }}">@csrf
                            <input type="password" name="password" placeholder="New password" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <input type="password" name="password_confirmation" placeholder="Confirm password" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 rounded-xl px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-sm font-medium transition-colors">Save Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exam History --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4"><i class="fas fa-history mr-2 text-indigo-500"></i>Exam History</h3>
            @forelse($student->examSessions as $session)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $session->exam?->title }}</p>
                    <p class="text-xs text-slate-400">{{ $session->submitted_at?->format('M d, Y') ?? 'In progress' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $session->percentage ? round($session->percentage, 1).'%' : '—' }}</p>
                    <span class="text-xs px-1.5 py-0.5 rounded
                        {{ $session->result_status==='Passed'?'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400':
                           ($session->result_status==='Conditional'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                           ($session->result_status==='Failed'?'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400':
                           'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400')) }}">
                        {{ $session->result_status }}
                    </span>
                </div>
            </div>
            @empty
            <x-ui.empty-state icon="fa-file-alt" title="No exams taken" subtitle="Exam history will appear here." />
            @endforelse
        </div>
    </div>

    {{-- Bio Notes --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4"><i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Counselor Bio-Notes</h3>

            {{-- Add Note --}}
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4 mb-6 border border-slate-200 dark:border-slate-700" x-data="{ open: false }">
                <button @click="open=!open" class="w-full text-left text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add New Bio-Note
                </button>
                <div x-show="open" x-cloak class="mt-4">
                    <form method="POST" action="{{ route('admin.bio-notes.store', $student->studentProfile) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Observation <span class="text-red-500">*</span></label>
                            <textarea name="observation" rows="3" required class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Follow-up Actions</label>
                            <textarea name="follow_up_actions" rows="2" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Follow-up Date</label>
                                <input type="date" name="follow_up_date" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                                <select name="status" class="w-full border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="open">Open</option>
                                    <option value="follow_up">Follow-up</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-save mr-1"></i>Save Note
                        </button>
                    </form>
                </div>
            </div>

            {{-- Notes List --}}
            <div class="space-y-4">
                @forelse($student->studentProfile?->bioNotes ?? [] as $note)
                <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="font-medium text-sm text-slate-800 dark:text-slate-200">{{ $note->counselor?->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-slate-400 ml-2">{{ $note->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $note->status==='open'?'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400':
                                   ($note->status==='follow_up'?'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400':
                                   'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400') }}">
                                {{ ucfirst(str_replace('_',' ',$note->status)) }}
                            </span>
                            <x-ui.confirm-modal
                                :action="route('admin.bio-notes.destroy', $note)"
                                method="DELETE"
                                title="Delete this note?"
                                message="This bio-note will be permanently deleted."
                                confirm-label="Delete Note"
                                trigger-class="text-slate-300 hover:text-red-500 dark:hover:text-red-400 text-xs">
                                <i class="fas fa-trash"></i>
                            </x-ui.confirm-modal>
                        </div>
                    </div>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $note->observation }}</p>
                    @if($note->follow_up_actions)
                    <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-1">Follow-up Actions:</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $note->follow_up_actions }}</p>
                    </div>
                    @endif
                    @if($note->follow_up_date)
                    <p class="text-xs text-slate-400 mt-2"><i class="fas fa-calendar mr-1"></i>Follow-up: {{ $note->follow_up_date->format('M d, Y') }}</p>
                    @endif
                </div>
                @empty
                <x-ui.empty-state icon="fa-sticky-note" title="No bio-notes yet" subtitle="Add the first counselor note above." />
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
