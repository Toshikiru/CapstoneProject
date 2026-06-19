<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->exam->title }} — TPC Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        [x-cloak] { display: none !important; }
        .question-card { scroll-margin-top: 80px; }
    </style>
</head>
<body class="bg-slate-100 font-sans" x-data="examApp()" x-init="init()">

{{-- Fixed Top Bar --}}
<div class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-200 shadow-sm h-14 flex items-center justify-between px-4 md:px-6">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-graduation-cap text-white text-xs"></i>
        </div>
        <p class="font-semibold text-slate-800 text-sm truncate hidden sm:block">{{ Str::limit($session->exam->title, 40) }}</p>
    </div>

    {{-- Timer --}}
    <div class="flex items-center gap-2 px-4 py-1.5 rounded-full border-2 font-mono font-bold text-lg"
         :class="timeLeft < 300 ? 'border-red-400 text-red-600 bg-red-50 animate-pulse' : timeLeft < 600 ? 'border-yellow-400 text-yellow-600 bg-yellow-50' : 'border-blue-300 text-blue-700 bg-blue-50'">
        <i class="fas fa-clock text-sm"></i>
        <span x-text="formatTime(timeLeft)"></span>
    </div>

    {{-- Progress --}}
    <div class="hidden md:flex items-center gap-2">
        <span class="text-xs text-slate-500"><span x-text="answeredCount"></span>/{{ $session->exam->total_questions }} answered</span>
        <div class="w-24 bg-slate-200 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full transition-all" :style="'width:' + (answeredCount/{{ $session->exam->total_questions }}*100) + '%'"></div>
        </div>
    </div>
</div>

{{-- Focus Loss Warning --}}
<div x-show="focusWarning" x-cloak
     class="fixed inset-0 z-[100] bg-red-900/80 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Tab Switch Detected!</h3>
        <p class="text-slate-600 text-sm mb-2">You have left the exam window <strong x-text="focusCount"></strong> time(s).</p>
        <p class="text-red-600 text-xs mb-6">Repeated violations may invalidate your exam session.</p>
        <button @click="focusWarning = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold">
            Return to Exam
        </button>
    </div>
</div>

{{-- Submit Confirmation --}}
<div x-show="showConfirm" x-cloak
     class="fixed inset-0 z-[100] bg-slate-900/70 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl">
        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-paper-plane text-blue-600 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2 text-center">Submit Exam?</h3>
        <p class="text-slate-600 text-sm text-center mb-1">You have answered <strong x-text="answeredCount"></strong> of <strong>{{ $session->exam->total_questions }}</strong> questions.</p>
        <p class="text-slate-500 text-xs text-center mb-6">This action cannot be undone.</p>
        <div class="flex gap-3">
            <button @click="showConfirm = false" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl font-medium hover:bg-slate-50">Cancel</button>
            <button @click="submitExam()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-semibold">
                <i class="fas fa-paper-plane mr-1"></i>Submit
            </button>
        </div>
    </div>
</div>

{{-- Auto-save indicator --}}
<div x-show="saving" x-cloak class="fixed bottom-4 left-4 bg-slate-800 text-white text-xs px-3 py-2 rounded-full flex items-center gap-2">
    <i class="fas fa-spinner fa-spin"></i> Auto-saving...
</div>
<div x-show="saved" x-cloak x-init="$watch('saved', v => v && setTimeout(() => saved=false, 2000))"
     class="fixed bottom-4 left-4 bg-green-600 text-white text-xs px-3 py-2 rounded-full flex items-center gap-2">
    <i class="fas fa-check"></i> Saved
</div>

{{-- Main Content --}}
<div class="pt-20 pb-24 max-w-3xl mx-auto px-4">
    {{-- Exam Header --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
        <h2 class="font-bold text-slate-800 text-lg">{{ $session->exam->title }}</h2>
        @if($session->exam->instructions)
        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $session->exam->instructions }}</p>
        @endif
        <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
            <span><i class="fas fa-list mr-1"></i>{{ $session->exam->total_questions }} questions</span>
            <span><i class="fas fa-star mr-1"></i>{{ $session->exam->total_points }} points</span>
            <span><i class="fas fa-clock mr-1"></i>{{ $session->exam->time_limit }} minutes</span>
        </div>
    </div>

    {{-- Questions by Section --}}
    @php $qNum = 0; @endphp
    @foreach($session->exam->sections as $section)
    <div class="mb-6">
        <div class="bg-blue-600 text-white rounded-xl px-5 py-3 mb-4">
            <h3 class="font-bold">Section {{ $loop->iteration }}: {{ $section->title }}</h3>
            @if($section->instructions)<p class="text-blue-100 text-xs mt-1">{{ $section->instructions }}</p>@endif
        </div>

        @foreach($section->questions as $question)
        @php $qNum++; @endphp
        <div id="q{{ $question->id }}" class="question-card bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-3"
             :class="answers['{{ $question->id }}'] ? 'border-blue-200' : ''">
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-600 flex-shrink-0 mt-0.5">{{ $qNum }}</span>
                <div class="flex-1">
                    <p class="text-slate-800 font-medium leading-relaxed mb-3">{{ $question->question_text }}</p>

                    {{-- Multiple Choice --}}
                    @if($question->type === 'multiple_choice')
                    <div class="space-y-2">
                        @foreach($question->options as $opt)
                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors"
                               :class="answers['{{ $question->id }}'] === '{{ $opt }}' ? 'border-blue-400 bg-blue-50' : 'border-slate-200'">
                            <input type="radio" name="q{{ $question->id }}" value="{{ $opt }}"
                                   x-model="answers['{{ $question->id }}']"
                                   @change="markAnswered('{{ $question->id }}')"
                                   {{ in_array($question->id, $answeredIds) ? '' : '' }}
                                   class="text-blue-600 focus:ring-blue-400">
                            <span class="text-sm text-slate-700">{{ $opt }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- True or False --}}
                    @elseif($question->type === 'true_or_false')
                    <div class="flex gap-3">
                        @foreach(['True','False'] as $opt)
                        <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors"
                               :class="answers['{{ $question->id }}'] === '{{ $opt }}' ? 'border-blue-400 bg-blue-50 font-semibold text-blue-700' : 'border-slate-200'">
                            <input type="radio" name="q{{ $question->id }}" value="{{ $opt }}"
                                   x-model="answers['{{ $question->id }}']"
                                   @change="markAnswered('{{ $question->id }}')"
                                   class="text-blue-600 focus:ring-blue-400">
                            <span class="text-sm">{{ $opt }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Likert Scale --}}
                    @elseif($question->type === 'likert_scale')
                    <div class="space-y-2">
                        @foreach($question->options as $i => $opt)
                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors"
                               :class="answers['{{ $question->id }}'] === '{{ $i+1 }}' ? 'border-blue-400 bg-blue-50' : 'border-slate-200'">
                            <input type="radio" name="q{{ $question->id }}" value="{{ $i+1 }}"
                                   x-model="answers['{{ $question->id }}']"
                                   @change="markAnswered('{{ $question->id }}')"
                                   class="text-blue-600 focus:ring-blue-400">
                            <span class="text-sm text-slate-700">{{ $opt }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Short Answer --}}
                    @elseif($question->type === 'short_answer')
                    <textarea name="q{{ $question->id }}" rows="4"
                              x-model="answers['{{ $question->id }}']"
                              @input="markAnswered('{{ $question->id }}')"
                              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                              placeholder="Type your answer here..."></textarea>
                    @endif

                    {{-- Point indicator --}}
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-slate-400">{{ $question->points }} point(s)</span>
                        <span x-show="answers['{{ $question->id }}']" class="text-xs text-green-600 font-medium">
                            <i class="fas fa-check-circle"></i> Answered
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

{{-- Fixed Bottom Bar --}}
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 flex items-center justify-between">
    <div class="text-sm text-slate-500">
        <span x-text="answeredCount" class="font-bold text-slate-800"></span>/{{ $session->exam->total_questions }} answered
    </div>
    <button @click="showConfirm = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-semibold flex items-center gap-2">
        <i class="fas fa-paper-plane"></i>
        Submit Exam
    </button>
</div>

@push('scripts')
<script>
function examApp() {
    return {
        answers: {},
        timeLeft: {{ $session->time_remaining_in_seconds }},
        answeredCount: 0,
        saving: false,
        saved: false,
        showConfirm: false,
        submitted: false,
        focusWarning: false,
        focusCount: 0,
        timerInterval: null,
        autoSaveInterval: null,
        syncInterval: null,

        init() {
            // Pre-populate answered questions
            @foreach($session->responses as $resp)
            this.answers['{{ $resp->question_id }}'] = @json($resp->answer);
            @endforeach

            this.countAnswered();
            this.startTimer();
            this.startAutoSave();
            this.startSyncTimer();
            this.setupFocusDetection();

            // Warn before leaving
            window.addEventListener('beforeunload', (e) => {
                if (this.timeLeft > 0) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timeLeft <= 0) {
                    clearInterval(this.timerInterval);
                    this.autoSubmit();
                    return;
                }
                this.timeLeft--;
            }, 1000);
        },

        startAutoSave() {
            this.autoSaveInterval = setInterval(() => this.doAutoSave(), 10000);
        },

        startSyncTimer() {
            this.syncInterval = setInterval(async () => {
                try {
                    const r = await fetch('{{ route('student.exam.sync-timer', $session) }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const d = await r.json();
                    if (d.status === 'expired') {
                        this.autoSubmit();
                    } else {
                        // Sync if off by more than 5 seconds
                        if (Math.abs(this.timeLeft - d.time_remaining) > 5) {
                            this.timeLeft = d.time_remaining;
                        }
                    }
                } catch(e) {}
            }, 30000);
        },

        setupFocusDetection() {
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.focusCount++;
                    this.reportFocusLoss();
                    if (this.focusCount >= 2) {
                        this.focusWarning = true;
                    }
                }
            });
        },

        async reportFocusLoss() {
            try {
                await fetch('{{ route('student.exam.focus-lost', $session) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch(e) {}
        },

        markAnswered(qId) {
            this.countAnswered();
        },

        countAnswered() {
            this.answeredCount = Object.values(this.answers).filter(v => v !== null && v !== undefined && v !== '').length;
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2,'0');
            const s = (seconds % 60).toString().padStart(2,'0');
            return `${m}:${s}`;
        },

        async doAutoSave() {
            const answerArray = Object.entries(this.answers).map(([id, value]) => ({ id, value }));
            if (answerArray.length === 0) return;

            this.saving = true;
            try {
                const r = await fetch('{{ route('student.exam.auto-save', $session) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ answers: answerArray, time_remaining: this.timeLeft })
                });
                const d = await r.json();
                if (d.status === 'expired') {
                    this.saving = false;
                    if (!this.submitted) this.autoSubmit();
                    return;
                }
                this.saving = false;
                this.saved = true;
            } catch(e) {
                this.saving = false;
            }
        },

        async autoSubmit() {
            if (this.submitted) return;
            this.submitted = true;
            clearInterval(this.timerInterval);
            clearInterval(this.autoSaveInterval);
            clearInterval(this.syncInterval);
            this.timeLeft = 0;
            try {
                await this.doAutoSave();
            } catch (e) {
                // Proceed with submission even if the final save fails —
                // we still want to lock in whatever was last saved rather
                // than leave the session stuck in-progress past its deadline.
            }
            document.getElementById('submitForm').submit();
        },

        async submitExam() {
            if (this.submitted) return;
            this.submitted = true;
            this.showConfirm = false;
            await this.doAutoSave();
            document.getElementById('submitForm').submit();
        }
    };
}
</script>

<form id="submitForm" method="POST" action="{{ route('student.exam.submit', $session) }}" style="display:none">
    @csrf
</form>
@endpush
</body>
</html>
