<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function markSingleAsMarked(string $id): void
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function deleteNotification(string $id): void
    {
        $notification = auth()->user()->notifications()->find($id);
        $notification->delete();
    }
}
