<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSentimentJob;
use App\Models\Conversation;
use App\Services\AIService;
use Illuminate\Http\Request;

class SentimentController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function index(Request $request)
    {
        $conversations = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['participants', 'latestMessage'])
            ->latest('updated_at')
            ->paginate(12);

        return view('ai.sentiment-index', compact('conversations'));
    }

    public function show(Conversation $conversation, Request $request)
    {
        $this->authorize('view', $conversation);

        $conversation->load('participants');
        $conversation->setRelation(
            'messages',
            $conversation->messagesChronological()->with('sender')->get()
        );

        if (!$conversation->sentiment_analyzed_at || $request->boolean('refresh')) {
            if (config('queue.default') === 'sync') {
                (new ProcessSentimentJob($conversation))->handle($this->aiService);
                $conversation->refresh();
            } else {
                ProcessSentimentJob::dispatch($conversation);
            }
        }

        $breakdown = $conversation->sentiment_breakdown ?? [];
        $sentiment = [
            'score' => $conversation->sentiment_score ?? 50,
            'label' => $conversation->sentiment_label ?? 'Neutral',
            'positive_percent' => $breakdown['positive_percent'] ?? 33,
            'neutral_percent' => $breakdown['neutral_percent'] ?? 34,
            'negative_percent' => $breakdown['negative_percent'] ?? 33,
            'message_count' => $breakdown['message_count'] ?? $conversation->messages->count(),
            'summary' => $breakdown['summary'] ?? '',
        ];

        return view('ai.sentiment', compact('conversation', 'sentiment'));
    }
}
