{{--
    Empty state placeholder for tables/lists with no data.
    Usage: <x-ui.empty-state icon="fa-users" title="No students yet" subtitle="Add your first student to get started." />
--}}
@props(['icon' => 'fa-inbox', 'title' => 'Nothing here yet', 'subtitle' => null])

<div class="flex flex-col items-center justify-center text-center py-16 px-4">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
        <i class="fas {{ $icon }} text-2xl text-slate-300 dark:text-slate-600"></i>
    </div>
    <p class="font-semibold text-slate-600 dark:text-slate-300">{{ $title }}</p>
    @if($subtitle)
        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 max-w-sm">{{ $subtitle }}</p>
    @endif
    @if(isset($slot) && trim($slot))
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
