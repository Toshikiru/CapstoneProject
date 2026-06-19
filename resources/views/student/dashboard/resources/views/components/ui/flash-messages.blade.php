{{--
    Flash message banner, reads from session('success'), session('error'), session('info'),
    and validation $errors automatically. Auto-dismisses success messages after 5s.
--}}
<div class="space-y-3 mb-4">
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 rounded-xl px-4 py-3 flex items-center justify-between gap-3">
            <span class="flex items-center gap-2 text-sm"><i class="fas fa-check-circle"></i>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-200"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 text-blue-800 dark:text-blue-300 rounded-xl px-4 py-3 flex items-center justify-between gap-3">
            <span class="flex items-center gap-2 text-sm"><i class="fas fa-info-circle"></i>{{ session('info') }}</span>
            <button @click="show = false" class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-200"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-800 dark:text-red-300 rounded-xl px-4 py-3">
            <div class="flex items-start justify-between gap-3">
                <div class="text-sm">
                    <p class="flex items-center gap-2 font-medium"><i class="fas fa-exclamation-circle"></i>
                        @if(session('error')){{ session('error') }}@else Please fix the following: @endif
                    </p>
                    @if($errors->any())
                        <ul class="mt-1.5 list-disc list-inside space-y-0.5 ml-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600 dark:hover:text-red-200 flex-shrink-0"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif
</div>
