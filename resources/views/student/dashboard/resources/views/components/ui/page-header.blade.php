{{--
    Standard page header: breadcrumbs, title, subtitle, optional back link, and an actions slot.
    Usage:
    <x-ui.page-header title="Students" subtitle="Manage student accounts" :breadcrumbs="[['label'=>'Students']]">
        <a href="..." class="...">Add Student</a>
    </x-ui.page-header>
--}}
@props(['title', 'subtitle' => null, 'breadcrumbs' => [], 'back' => null])

<div class="mb-6">
    @if(count($breadcrumbs))
        <x-ui.breadcrumbs :items="$breadcrumbs" />
    @endif

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3 min-w-0">
            @if($back)
                <a href="{{ $back }}"
                   class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-700 hover:border-slate-300 dark:border-slate-700 dark:text-slate-500 dark:hover:text-slate-200 dark:hover:border-slate-600 transition-colors flex-shrink-0"
                   aria-label="Go back">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
            @endif
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 truncate">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if(isset($slot) && trim($slot))
            <div class="flex items-center gap-2 flex-wrap">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
