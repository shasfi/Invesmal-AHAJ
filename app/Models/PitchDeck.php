<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PitchDeck extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'startup_description',
        'content_json',
        'file_path',
        'file_type',
        'status',
        'ai_analysis',
        'ai_summary',
        'ai_score',
    ];

    protected $casts = [
        'content_json' => 'array',
        'ai_analysis' => 'array',
        'ai_summary' => 'array',
    ];

    protected $hidden = [
        'ai_analysis',
    ];

    /**
     * The user who owns this pitch deck.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Startups linked to this pitch deck.
     */
    public function startups(): HasMany
    {
        return $this->hasMany(Startup::class);
    }

    /**
     * Scope: only finalized / analyzed decks for public viewing.
     */
    public function scopePublicViewable($query)
    {
        return $query->whereIn('status', ['analyzed', 'final']);
    }

    /**
     * Get the public-safe summary (hide sensitive sections).
     */
    public function getPublicSummaryAttribute(): ?array
    {
        if (!$this->ai_summary) {
            return null;
        }
        $summary = $this->ai_summary;
        // Exclude financial projections from public view
        unset($summary['financial_projections']);
        return $summary;
    }

    /**
     * Check if this deck has an uploaded file.
     */
    public function hasUploadedFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Check if AI-generated content exists.
     */
    public function isGenerated(): bool
    {
        return !empty($this->content_json);
    }
}