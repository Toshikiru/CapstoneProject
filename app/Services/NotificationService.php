<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function notifyAdmins(string $type, string $title, string $message, array $data = []): void
    {
        $admins = User::where('role', 'admin')->where('is_active', true)->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
                'data'    => $data ?: null,
            ]);
        }
    }

    public static function notifyUser(int $userId, string $type, string $title, string $message, array $data = []): void
    {
        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data ?: null,
        ]);
    }
}
