<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Services\AIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSentimentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Conversation $conversation) {}

    public function handle(AIService $aiService): void
    {
        $messages = $this->conversation->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        $sentiment = $aiService->analyzeSentiment($messages);

        $this->conversation->update([
            'sentiment_score' => $sentiment['score'],
            'sentiment_label' => $sentiment['label'],
            'sentiment_breakdown' => [
                'positive_percent' => $sentiment['positive_percent'],
                'neutral_percent' => $sentiment['neutral_percent'],
                'negative_percent' => $sentiment['negative_percent'],
                'summary' => $sentiment['summary'] ?? '',
                'message_count' => $sentiment['message_count'] ?? $messages->count(),
            ],
            'sentiment_analyzed_at' => now(),
        ]);
    }
}
