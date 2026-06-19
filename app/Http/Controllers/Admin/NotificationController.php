<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->markAsRead();
        return back();
    }

    public function markAllRead(): JsonResponse
    {
        auth()->user()->notifications()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json(['count' => auth()->user()->unreadNotifications()->count()]);
    }
}
