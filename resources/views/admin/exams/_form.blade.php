@php $exam = $exam ?? null; @endphp
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100"><i class="fas fa-file-alt mr-2 text-blue-500"></i>Exam Details</h3>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $exam?->title ?? '') }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $exam?->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Instructions</label>
        <textarea name="instructions" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('instructions', $exam?->instructions ?? '') }}</textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Time Limit (minutes) <span class="text-red-500">*</span></label>
            <input type="number" name="time_limit" value="{{ old('time_limit', $exam?->time_limit ?? 60) }}" min="1" max="480"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Passing Score (%) <span class="text-red-500">*</span></label>
            <input type="number" name="passing_score" value="{{ old('passing_score', $exam?->passing_score ?? 75) }}" min="0" max="100" step="0.01"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Max Attempts</label>
            <input type="number" name="max_attempts" value="{{ old('max_attempts', $exam?->max_attempts ?? 1) }}" min="1"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available From</label>
            <input type="datetime-local" name="available_from" value="{{ old('available_from', $exam?->available_from?->format('Y-m-d\TH:i') ?? '') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available Until</label>
            <input type="datetime-local" name="available_until" value="{{ old('available_until', $exam?->available_until?->format('Y-m-d\TH:i') ?? '') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>
