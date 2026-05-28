<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'scheduler_id', 'invitee_id', 'startup_id', 'title',
        'notes', 'scheduled_at', 'status', 'location',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduler_id');
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeUpcoming($query) { return $query->whereIn('status', ['pending','accepted'])->where('scheduled_at', '>=', now()); }
}