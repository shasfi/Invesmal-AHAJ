<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_investment_updates',
        'email_meeting_updates',
        'email_message_notifications',
        'email_verification_updates',
        'in_app_investment_updates',
        'in_app_meeting_updates',
        'in_app_message_notifications',
        'in_app_verification_updates',
    ];

    protected function casts(): array
    {
        return array_fill_keys([
            'email_investment_updates',
            'email_meeting_updates',
            'email_message_notifications',
            'email_verification_updates',
            'in_app_investment_updates',
            'in_app_meeting_updates',
            'in_app_message_notifications',
            'in_app_verification_updates',
        ], 'boolean');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultAttributes(): array
    {
        return [
            'email_investment_updates' => true,
            'email_meeting_updates' => true,
            'email_message_notifications' => true,
            'email_verification_updates' => true,
            'in_app_investment_updates' => true,
            'in_app_meeting_updates' => true,
            'in_app_message_notifications' => true,
            'in_app_verification_updates' => true,
        ];
    }

    public static function allowsEmail(User $user, string $notificationType): bool
    {
        $prefs = $user->notificationPreference;

        if (!$prefs) {
            return true;
        }

        return match ($notificationType) {
            'meeting_scheduled' => $prefs->email_meeting_updates,
            'investment_requested', 'investment_status_changed' => $prefs->email_investment_updates,
            'new_message' => $prefs->email_message_notifications,
            'startup_verified', 'user_registered', 'startup_created', 'document_uploaded' => $prefs->email_verification_updates,
            default => true,
        };
    }

    public static function allowsInApp(User $user, string $notificationType): bool
    {
        $prefs = $user->notificationPreference;

        if (!$prefs) {
            return true;
        }

        return match ($notificationType) {
            'meeting_scheduled' => $prefs->in_app_meeting_updates,
            'investment_requested', 'investment_status_changed' => $prefs->in_app_investment_updates,
            'new_message' => $prefs->in_app_message_notifications,
            'startup_verified', 'user_registered', 'startup_created', 'document_uploaded' => $prefs->in_app_verification_updates,
            default => true,
        };
    }
}
