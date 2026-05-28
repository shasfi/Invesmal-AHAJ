<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\User;

trait HasNotifications
{
    public function sendNotification(User $user, string $type, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    public function sendNotificationToMany(array $userIds, string $type, array $data = []): void
    {
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'data' => $data,
                'read_at' => null,
            ]);
        }
    }
}