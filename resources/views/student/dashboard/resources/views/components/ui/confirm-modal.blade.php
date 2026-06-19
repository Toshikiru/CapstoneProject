{{--
    Confirmation modal for destructive actions (delete, deactivate, etc).
    Wraps a form; clicking the trigger button opens the modal instead of submitting immediately.

    Usage:
    <x-ui.confirm-modal
        :action="route('admin.students.destroy', $student)"
        method="DELETE"
        title="Delete student?"
        message="This will permanently remove {{ $student->name }} and all related records. This cannot be undone."
        confirm-label="Delete"
        trigger-class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400">
        <i class="fas fa-trash"></i>
    </x-ui.confirm-modal>
--}}
@props([
    'action',
    'method' => 'POST',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'triggerClass' => 'text-red-500 hover:text-red-700 text-sm',
    'danger' => true,
])

<div x-data="{ open: false }" class="inline-block">
    <button type="button" @click="open = true" class="{{ $triggerClass }}">
        {{ $slot }}
    </button>

    <template x-teleport="body">
        <div x-show="open" x-cloak
             class="fixed inset-0 z-[200] flex items-center justify-center p-4"
             style="display: none;">
            <div x-show="open" x-transition.opacity @click="open = false"
                 class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="open" x-transition.scale.origin.top
                 class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6"
                 @keydown.escape.window="open = false">
                <div class="w-12 h-12 rounded-full {{ $danger ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' }} flex items-center justify-center mb-4">
                    <i class="fas {{ $danger ? 'fa-exclamation-triangle' : 'fa-question-circle' }} text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1.5">{{ $title }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ $message }}</p>

                <div class="flex gap-2">
                    <button type="button" @click="open = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                        {{ $cancelLabel }}
                    </button>
                    <form method="POST" action="{{ $action }}" class="flex-1">
                        @csrf
                        @if(strtoupper($method) !== 'POST')
                            @method($method)
                        @endif
                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-xl font-medium text-sm text-white transition-colors {{ $danger ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                            {{ $confirmLabel }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
