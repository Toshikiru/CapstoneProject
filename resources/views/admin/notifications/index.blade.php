@extends('layouts.admin')
@section('title', 'Notifications')
@section('content')

<x-ui.page-header title="Notifications" subtitle="System and examination notifications" :breadcrumbs="[['label'=>'Notifications']]"/>

<div class="space-y-3">
    @forelse($notifications as $notif)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 flex items-start gap-4 {{ !$notif->is_read ? 'border-l-4 border-l-blue-500' : '' }}">
        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
            {{ $notif->type==='result_ready' ? 'bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400' :
               ($notif->type==='exam_submitted' ? 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' :
               'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400') }}">
            <i class="fas {{ $notif->type==='result_ready' ? 'fa-check-circle' : ($notif->type==='exam_submitted' ? 'fa-file-alt' : 'fa-bell') }}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $notif->title }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $notif->message }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        @if(!$notif->is_read)
        <form method="POST" action="{{ route('admin.notifications.mark-read', $notif) }}">@csrf @method('PATCH')
            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex-shrink-0">Mark read</button>
        </form>
        @endif
    </div>
    @empty
        <x-ui.empty-state icon="fa-bell" title="No notifications" subtitle="System notifications will appear here." />
    @endforelse

    @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
