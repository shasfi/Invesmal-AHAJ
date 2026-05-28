<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'subject',
        'sentiment_score',
        'sentiment_label',
        'sentiment_breakdown',
        'sentiment_analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'sentiment_breakdown' => 'array',
            'sentiment_analyzed_at' => 'datetime',
        ];
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function messagesChronological(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at')->orderBy('id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}