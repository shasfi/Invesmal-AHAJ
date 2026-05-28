<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'startup_id', 'type', 'filename', 'original_name',
        'path', 'version', 'size', 'mime_type', 'description',
    ];

    protected $casts = [
        'version' => 'integer',
        'size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }

    public function scopePitchDecks($query) { return $query->where('type', 'pitch_deck'); }
    public function scopeBusinessPlans($query) { return $query->where('type', 'business_plan'); }
}