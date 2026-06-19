<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Slip — {{ $session->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print { .no-print { display: none; } body { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }</style>
</head>
<body class="bg-white p-8 max-w-2xl mx-auto">
    <div class="no-print mb-6 text-center">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
            <i class="fas fa-print mr-2"></i>Print Slip
        </button>
        <a href="{{ route('admin.results.show', $session) }}" class="ml-3 px-6 py-2 rounded-lg border border-slate-200 text-slate-600">← Back</a>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <div class="border-2 border-slate-800 rounded-xl p-8">
        {{-- Header --}}
        <div class="text-center mb-6 pb-6 border-b-2 border-slate-200">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-graduation-cap text-white text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-800">Talibon Polytechnic College</h1>
            <p class="text-slate-500 text-sm">Talibon, Bohol, Philippines</p>
            <p class="text-slate-500 text-sm">Guidance Services Office</p>
            <div class="mt-3 inline-block bg-blue-600 text-white px-6 py-1.5 rounded-full text-sm font-bold">
                ADMISSION SLIP
            </div>
        </div>

        {{-- Student Info --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            @php $p = $session->user->studentProfile; @endphp
            <div><p class="text-xs text-slate-500 font-medium">Student Name</p><p class="font-bold text-slate-800">{{ $p?->full_name ?? $session->user->name }}</p></div>
            <div><p class="text-xs text-slate-500 font-medium">Student ID</p><p class="font-bold font-mono text-slate-800">{{ $session->user->student_id }}</p></div>
            <div><p class="text-xs text-slate-500 font-medium">Course</p><p class="font-medium text-slate-700">{{ $p?->course ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500 font-medium">Year Level</p><p class="font-medium text-slate-700">{{ $p?->year_level ?? '—' }}</p></div>
        </div>

        {{-- Exam Result --}}
        <div class="bg-slate-50 rounded-xl p-5 mb-6">
            <h3 class="font-semibold text-slate-700 mb-3 text-sm">EXAMINATION RESULT</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-800">{{ $session->raw_score ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">Raw Score</p>
                </div>
                <div class="text-center border-x border-slate-200">
                    <p class="text-3xl font-bold text-slate-800">{{ $session->percentage ? round($session->percentage,1).'%' : '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">Percentage</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-800">{{ $session->interpretation }}</p>
                    <p class="text-xs text-slate-500 mt-1">Interpretation</p>
                </div>
            </div>
        </div>

        {{-- Admission Status --}}
        @php $r = $session->result_status; @endphp
        <div class="text-center mb-6">
            <p class="text-sm text-slate-600 mb-2">ADMISSION STATUS</p>
            <div class="inline-block px-8 py-3 rounded-xl text-2xl font-bold
                {{ $r==='Passed'?'bg-green-100 text-green-700 border-2 border-green-300':($r==='Conditional'?'bg-yellow-100 text-yellow-700 border-2 border-yellow-300':($r==='Failed'?'bg-red-100 text-red-700 border-2 border-red-300':'bg-slate-100 text-slate-700 border-2 border-slate-300')) }}">
                {{ $r }}
            </div>
        </div>

        {{-- Exam Details --}}
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div><p class="text-xs text-slate-500">Exam Title</p><p class="font-medium text-slate-700">{{ $session->exam->title }}</p></div>
            <div><p class="text-xs text-slate-500">Date Taken</p><p class="font-medium text-slate-700">{{ $session->submitted_at?->format('F d, Y') }}</p></div>
        </div>

        {{-- Signatures --}}
        <div class="grid grid-cols-2 gap-8 mt-8 pt-6 border-t border-slate-200">
            <div class="text-center">
                <div class="h-12 border-b border-slate-400 mb-2"></div>
                <p class="text-xs text-slate-600 font-medium">Student's Signature</p>
            </div>
            <div class="text-center">
                <div class="h-12 border-b border-slate-400 mb-2"></div>
                <p class="text-xs text-slate-600 font-medium">Guidance Counselor</p>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-4">Generated: {{ now()->format('F d, Y h:i A') }} — TPC Guidance Services System</p>
    </div>
</body>
</html>
