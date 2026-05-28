<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait HasActivityLog
{
    public function logActivity(string $action, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    public static function logStaticActivity(string $action, ?string $description = null, ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}