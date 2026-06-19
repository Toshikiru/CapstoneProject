{{--
    Breadcrumb navigation component.
    Usage: <x-ui.breadcrumbs :items="[['label' => 'Students', 'url' => route('admin.students.index')], ['label' => 'Juan Dela Cruz']]" />
    The last item (no 'url' key, or omitted) renders as plain text (current page).
--}}
@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex items-center gap-1.5 text-sm flex-wrap">
        <li class="flex items-center gap-1.5">
            <a href="{{ request()->is('admin*') ? route('admin.dashboard') : route('student.dashboard') }}"
               class="text-slate-400 hover:text-blue-600 dark:text-slate-500 dark:hover:text-blue-400 transition-colors">
                <i class="fas fa-home text-xs"></i>
            </a>
        </li>
        @foreach($items as $item)
            <li class="flex items-center gap-1.5">
                <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-slate-700 dark:text-slate-200 font-medium">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
