<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notifyNow(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notifyNow(new ResetPasswordNotification($token));
    }

    public const ROLE_STUDENT_FOUNDER = 'student_founder';
    public const ROLE_INVESTOR = 'investor';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_STUDENT_FOUNDER,
        self::ROLE_INVESTOR,
        self::ROLE_MENTOR,
        self::ROLE_ADMIN,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_verified',
        'status',
        'bio',
        'university',
        'oauth_provider',
        'oauth_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function startups(): HasMany
    {
        return $this->hasMany(Startup::class, 'founder_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class, 'investor_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function scheduledMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'scheduler_id');
    }

    public function invitedMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'invitee_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function pitchDecks(): HasMany
    {
        return $this->hasMany(PitchDeck::class);
    }

    public function scopeStudentFounders($query)
    {
        return $query->where('role', self::ROLE_STUDENT_FOUNDER);
    }

    public function scopeInvestors($query)
    {
        return $query->where('role', self::ROLE_INVESTOR);
    }

    public function scopeMentors($query)
    {
        return $query->where('role', self::ROLE_MENTOR);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=184343&color=E3D2C0&size=80';
    }

    /** Same email can exist per role — password reset tokens must be role-specific. */
    public function getEmailForPasswordReset(): string
    {
        return $this->email . '|' . $this->role;
    }
}