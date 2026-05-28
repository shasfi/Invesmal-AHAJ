<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(string $action, ?int $userId, ?string $entityType = null, ?int $entityId = null, ?array $changes = null, ?Request $request = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'changes' => $changes,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public function getRecent(int $limit = 50)
    {
        return ActivityLog::with('user')->recent()->limit($limit)->get();
    }

    public function getForEntity(string $type, int $id, int $limit = 20)
    {
        return ActivityLog::with('user')->forEntity($type, $id)->recent()->limit($limit)->get();
    }
}