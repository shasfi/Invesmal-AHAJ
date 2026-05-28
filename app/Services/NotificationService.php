<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user with duplicate prevention (same type+title within 5 minutes).
     */
    public function notify(User $user, string $type, string $title, ?string $body = null, ?string $actionUrl = null, ?array $data = null): ?Notification
    {
        // Prevent duplicate notifications for the same type+title within the last 5 minutes
        $recentDuplicate = Notification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('title', $title)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($recentDuplicate) {
            return null;
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Create a notification and also send it via email if the user has a verified email.
     */
    public function notifyWithEmail(User $user, string $type, string $title, ?string $body = null, ?string $actionUrl = null, ?array $data = null): ?Notification
    {
        $notification = $this->notify($user, $type, $title, $body, $actionUrl, $data);

        if ($notification && $user->is_verified && $user->email_verified_at) {
            \Illuminate\Support\Facades\Mail::to($user)->queue(
                new \App\Mail\NotificationMail($user, $notification)
            );
        }

        return $notification;
    }

    public function getUnreadForUser(User $user, int $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->unread()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getAllForUser(User $user, int $perPage = 20)
    {
        return Notification::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)->unread()->count();
    }
}
