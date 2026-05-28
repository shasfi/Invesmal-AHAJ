<?php

namespace App\Services;

use App\Models\Startup;
use App\Models\User;

class VerificationService
{
    public function __construct(
        private ActivityLogService $activityLog,
        private NotificationService $notification,
    ) {}

    public function verifyUser(User $user, User $admin): void
    {
        $user->update(['is_verified' => true]);
        $this->activityLog->log('user_verified', $admin->id, 'User', $user->id, null, request());
        $this->notification->notify($user, 'success', 'Account Verified', 'Your account has been verified by an administrator.');
    }

    public function verifyStartup(Startup $startup, User $admin): void
    {
        $startup->update([
            'is_verified' => true,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);
        $this->activityLog->log('startup_verified', $admin->id, 'Startup', $startup->id, null, request());
        $this->notification->notify(
            $startup->founder,
            'success',
            'Startup Verified',
            "Your startup \"{$startup->name}\" has been verified.",
            route('startups.show', $startup)
        );
    }

    public function flagStartup(Startup $startup, ?string $reason, User $admin): void
    {
        $startup->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);
        $this->activityLog->log('startup_flagged', $admin->id, 'Startup', $startup->id, ['reason' => $reason], request());
        $this->notification->notify(
            $startup->founder,
            'warning',
            'Startup Flagged',
            "Your startup \"{$startup->name}\" has been flagged: {$reason}",
            route('startups.show', $startup)
        );
    }

    public function unflagStartup(Startup $startup, User $admin): void
    {
        $startup->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);
        $this->activityLog->log('startup_unflagged', $admin->id, 'Startup', $startup->id, null, request());
    }
}