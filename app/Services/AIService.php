<?php

namespace App\Services;

use OpenAI;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected ?Client $client = null;
    protected string $model;
    protected int $timeout;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct()
    {
        $config = config('ai.openai');

        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            Log::warning('AIService: OpenAI API key is not set. AI features will be unavailable.');
            $this->client = null;
        } else {
            $this->client = OpenAI::client($apiKey);
        }

        $this->model = $config['default_model'] ?? 'gpt-4o-mini';
        $this->timeout = $config['timeout'] ?? 60;
        $this->maxTokens = $config['max_tokens'] ?? 2000;
        $this->temperature = $config['temperature'] ?? 0.7;
    }

    /**
     * Send a prompt to OpenAI and get structured JSON response.
     */
    public function complete(string $systemPrompt, string $userPrompt, array $options = []): ?array
    {
        if ($this->client === null) {
            Log::error('AIService: Cannot complete - OpenAI client not initialized (missing API key).');
            return null;
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        try {
            $response = $this->client->chat()->create([
                'model' => $options['model'] ?? $this->model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                'temperature' => $options['temperature'] ?? $this->temperature,
                'response_format' => $options['response_format'] ?? ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content ?? null;

            if (!$content) {
                Log::error('AIService: Empty response from OpenAI');
                return null;
            }

            $decoded = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AIService: JSON decode failed', [
                    'error' => json_last_error_msg(),
                    'raw_preview' => substr($content, 0, 500),
                ]);
                return null;
            }

            return $decoded;

        } catch (\Exception $e) {
            Log::error('AIService: OpenAI API error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return null;
        }
    }

    /**
     * Send a streaming prompt (for real-time UX in Blade via SSE).
     * Returns generator yielding chunks.
     */
    public function stream(string $systemPrompt, string $userPrompt, array $options = []): \Generator
    {
        if ($this->client === null) {
            Log::error('AIService: Cannot stream - OpenAI client not initialized (missing API key).');
            yield "\n\n[AI features are currently unavailable. Please configure your OpenAI API key.]";
            return;
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        try {
            $stream = $this->client->chat()->createStreamed([
                'model' => $options['model'] ?? $this->model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                'temperature' => $options['temperature'] ?? $this->temperature,
            ]);

            foreach ($stream as $chunk) {
                $delta = $chunk->choices[0]->delta->content ?? '';
                if ($delta !== '') {
                    yield $delta;
                }
            }
        } catch (\Exception $e) {
            Log::error('AIService: Stream error', ['message' => $e->getMessage()]);
            yield "\n\n[AI generation interrupted. Please try again.]";
        }
    }

    /**
     * Get the default model name.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    public function isConfigured(): bool
    {
        return $this->client !== null;
    }

    /**
     * Analyze conversation messages for investor–startup sentiment.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection  $messages
     */
    public function analyzeSentiment($messages): array
    {
        $messageList = $messages->take(50);
        $count = $messageList->count();

        if ($count === 0) {
            return $this->defaultSentiment(0);
        }

        $transcript = $messageList->map(function ($message) {
            $sender = $message->sender?->name ?? 'User';
            return "{$sender}: {$message->body}";
        })->implode("\n");

        $systemPrompt = <<<'PROMPT'
You analyze investor-startup chat tone. Return ONLY valid JSON:
{
  "score": 75,
  "label": "Positive",
  "positive_percent": 60,
  "neutral_percent": 25,
  "negative_percent": 15,
  "summary": "One sentence insight."
}
Label must be exactly one of: Positive, Neutral, Negative. Score is 0-100.
PROMPT;

        $result = $this->complete($systemPrompt, "Analyze this conversation:\n\n{$transcript}", [
            'max_tokens' => 800,
            'temperature' => 0.4,
        ]);

        if ($result && isset($result['label'])) {
            return [
                'score' => (int) min(100, max(0, $result['score'] ?? 50)),
                'label' => in_array($result['label'], ['Positive', 'Neutral', 'Negative'], true)
                    ? $result['label'] : 'Neutral',
                'positive_percent' => (int) ($result['positive_percent'] ?? 33),
                'neutral_percent' => (int) ($result['neutral_percent'] ?? 34),
                'negative_percent' => (int) ($result['negative_percent'] ?? 33),
                'summary' => $result['summary'] ?? '',
                'message_count' => $count,
            ];
        }

        return $this->heuristicSentiment($messageList);
    }

    protected function heuristicSentiment($messages): array
    {
        $text = strtolower($messages->pluck('body')->implode(' '));
        $positive = ['great', 'excellent', 'interested', 'excited', 'good', 'thanks', 'approve', 'love', 'strong'];
        $negative = ['bad', 'concern', 'risk', 'reject', 'decline', 'worried', 'weak', 'poor', 'no'];

        $pos = 0;
        $neg = 0;
        foreach ($positive as $word) {
            $pos += substr_count($text, $word);
        }
        foreach ($negative as $word) {
            $neg += substr_count($text, $word);
        }

        $total = max(1, $pos + $neg);
        $positivePercent = (int) round(($pos / $total) * 100);
        $negativePercent = (int) round(($neg / $total) * 100);
        $neutralPercent = max(0, 100 - $positivePercent - $negativePercent);

        $score = (int) round(50 + ($positivePercent - $negativePercent) * 0.5);
        $score = min(100, max(0, $score));

        $label = $score >= 65 ? 'Positive' : ($score <= 40 ? 'Negative' : 'Neutral');

        return [
            'score' => $score,
            'label' => $label,
            'positive_percent' => $positivePercent,
            'neutral_percent' => $neutralPercent,
            'negative_percent' => $negativePercent,
            'summary' => 'Heuristic analysis based on message keywords (configure OPENAI_API_KEY for AI-powered sentiment).',
            'message_count' => $messages->count(),
        ];
    }

    protected function defaultSentiment(int $count): array
    {
        return [
            'score' => 50,
            'label' => 'Neutral',
            'positive_percent' => 33,
            'neutral_percent' => 34,
            'negative_percent' => 33,
            'summary' => 'No messages to analyze yet.',
            'message_count' => $count,
        ];
    }

    /**
     * Generate platform insight recommendations.
     */
    public function generateInsightRecommendations(array $context): array
    {
        $systemPrompt = 'You are a startup investment analyst. Return JSON: {"recommendations":[{"title":"...","description":"..."}]} with 3 items max.';
        $userPrompt = 'Platform data: ' . json_encode($context);

        $result = $this->complete($systemPrompt, $userPrompt, ['max_tokens' => 600, 'temperature' => 0.6]);

        if ($result && !empty($result['recommendations'])) {
            return $result['recommendations'];
        }

        return [
            ['title' => 'Focus on verified startups', 'description' => 'Prioritize startups with complete profiles and pitch decks for better due diligence.'],
            ['title' => 'Track engagement trends', 'description' => 'Monitor industries with rising investor interest on the discover page.'],
            ['title' => 'Schedule follow-up meetings', 'description' => 'Convert investment interest into meetings while momentum is high.'],
        ];
    }
}
