<?php

namespace App\Services;

use App\Models\PitchDeck;
use App\Models\Startup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PitchDeckService
{
    protected AIService $ai;

    protected DemoPitchDeckService $demo;

    public function __construct()
    {
        $this->ai = app(AIService::class);
        $this->demo = app(DemoPitchDeckService::class);
    }

    /**
     * Generate a full pitch deck from a startup description.
     */
    public function generate(string $startupDescription, ?string $title = null): ?array
    {
        $systemPrompt = <<<PROMPT
You are an expert venture capital pitch deck generator. Given a startup description, produce a structured JSON pitch deck with the following sections. Write compelling, investor-grade copy. Be specific, data-informed, and persuasive. Keep each section concise (2-5 sentences max).

Return ONLY valid JSON in this exact structure:
{
  "sections": [
    {
      "id": "problem",
      "title": "Problem",
      "content": "..."
    },
    {
      "id": "solution",
      "title": "Solution",
      "content": "..."
    },
    {
      "id": "market",
      "title": "Market Opportunity",
      "content": "..."
    },
    {
      "id": "product",
      "title": "Product & Technology",
      "content": "..."
    },
    {
      "id": "traction",
      "title": "Traction & Milestones",
      "content": "..."
    },
    {
      "id": "business_model",
      "title": "Business Model",
      "content": "..."
    },
    {
      "id": "competition",
      "title": "Competitive Landscape",
      "content": "..."
    },
    {
      "id": "team",
      "title": "Team",
      "content": "..."
    },
    {
      "id": "financials",
      "title": "Financial Projections",
      "content": "..."
    },
    {
      "id": "ask",
      "title": "The Ask",
      "content": "..."
    }
  ],
  "executive_summary": "A compelling 3-4 sentence summary that captures the entire vision.",
  "tagline": "One powerful sentence that sells the vision."
}
PROMPT;

        $userPrompt = $title
            ? "Generate a pitch deck titled \"{$title}\" for this startup:\n\n{$startupDescription}"
            : "Generate a pitch deck for this startup:\n\n{$startupDescription}";

        if (!$this->ai->isConfigured()) {
            Log::info('PitchDeckService: Using demo generator (OPENAI_API_KEY not set)');
            return $this->demo->generate($startupDescription, $title);
        }

        $result = $this->ai->complete($systemPrompt, $userPrompt, [
            'max_tokens' => 3000,
            'temperature' => 0.8,
        ]);

        if (!$result || !isset($result['sections'])) {
            Log::error('PitchDeckService: Generation failed - invalid AI response');
            return null;
        }

        unset($result['_demo_mode']);

        return $result;
    }

    /**
     * Analyze an uploaded pitch deck file and return detailed feedback.
     * (For now, we analyze based on extracted text context. Future: parse PDF/PPTX.)
     */
    public function analyze(PitchDeck $pitchDeck): ?array
    {
        $content = null;

        // If we have generated content, analyze that
        if ($pitchDeck->content_json && is_array($pitchDeck->content_json)) {
            $content = json_encode($pitchDeck->content_json);
        } elseif ($pitchDeck->startup_description) {
            $content = $pitchDeck->startup_description;
        } else {
            Log::warning('PitchDeckService: No content to analyze for deck #' . $pitchDeck->id);
            return null;
        }

        $systemPrompt = <<<PROMPT
You are a world-class pitch deck analyst. Evaluate the given pitch deck content and return a detailed JSON analysis. Be brutally honest and constructive. Score each category 1-10.

Return ONLY valid JSON in this exact structure:
{
  "overall_score": 72,
  "verdict": "A one-sentence overall assessment.",
  "strengths": ["strength 1", "strength 2", "strength 3"],
  "weaknesses": ["weakness 1", "weakness 2"],
  "categories": {
    "clarity": {"score": 7, "comment": "..."},
    "problem_statement": {"score": 8, "comment": "..."},
    "solution_fit": {"score": 6, "comment": "..."},
    "market_opportunity": {"score": 8, "comment": "..."},
    "business_model": {"score": 6, "comment": "..."},
    "competitive_advantage": {"score": 7, "comment": "..."},
    "team_strength": {"score": 5, "comment": "..."},
    "financial_projections": {"score": 5, "comment": "..."},
    "ask_clarity": {"score": 6, "comment": "..."}
  },
  "key_improvements": ["actionable improvement 1", "actionable improvement 2", "actionable improvement 3"],
  "investor_readiness": "Not Ready | Approaching | Ready | Highly Investable"
}
PROMPT;

        $userPrompt = "Analyze this pitch deck:\n\n" . $content;

        if (!$this->ai->isConfigured()) {
            Log::info('PitchDeckService: Using demo analysis (OPENAI_API_KEY not set)');
            $result = $this->demo->analyze($content);
        } else {
            $result = $this->ai->complete($systemPrompt, $userPrompt, [
                'max_tokens' => 2500,
                'temperature' => 0.5,
            ]);

            if (!$result || !isset($result['categories'])) {
                Log::error('PitchDeckService: Analysis failed - invalid AI response');
                return null;
            }
        }

        unset($result['_demo_mode']);

        // Create public-safe summary
        $summary = $this->buildPublicSummary($result, $pitchDeck);

        // Update the pitch deck
        $pitchDeck->update([
            'ai_analysis' => $result,
            'ai_summary' => $summary,
            'ai_score' => $result['overall_score'] ?? null,
            'status' => 'analyzed',
        ]);

        return $result;
    }

    /**
     * Build a public-safe summary from the full analysis.
     */
    protected function buildPublicSummary(array $analysis, PitchDeck $pitchDeck): array
    {
        $sections = [];
        if ($pitchDeck->content_json && isset($pitchDeck->content_json['sections'])) {
            foreach ($pitchDeck->content_json['sections'] as $section) {
                $sections[$section['id']] = $section['content'] ?? '';
            }
        }

        return [
            'tagline' => $pitchDeck->content_json['tagline'] ?? '',
            'executive_summary' => $pitchDeck->content_json['executive_summary'] ?? '',
            'overall_score' => $analysis['overall_score'] ?? null,
            'verdict' => $analysis['verdict'] ?? '',
            'strengths' => array_slice($analysis['strengths'] ?? [], 0, 3),
            'market_opportunity' => $analysis['categories']['market_opportunity']['comment'] ?? '',
            'investor_readiness' => $analysis['investor_readiness'] ?? 'Not Rated',
            'problem_excerpt' => $sections['problem'] ?? '',
            'solution_excerpt' => $sections['solution'] ?? '',
        ];
    }

    /**
     * Save user-edited pitch deck content.
     */
    public function saveEdits(PitchDeck $pitchDeck, array $sections, string $executiveSummary, string $tagline): PitchDeck
    {
        $content = [
            'sections' => $sections,
            'executive_summary' => $executiveSummary,
            'tagline' => $tagline,
        ];

        $pitchDeck->update([
            'content_json' => $content,
            'status' => 'final',
        ]);

        return $pitchDeck->fresh();
    }

    /**
     * Get all pitch decks for a user.
     */
    public function getForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return PitchDeck::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get public-facing pitch deck data for a startup profile.
     */
    public function getPublicData(Startup $startup): ?array
    {
        if (!$startup->pitch_deck_id) {
            return null;
        }

        $deck = PitchDeck::publicViewable()->find($startup->pitch_deck_id);

        if (!$deck) {
            return null;
        }

        return [
            'title' => $deck->title,
            'score' => $deck->ai_score,
            'summary' => $deck->public_summary,
            'sections' => $deck->content_json['sections'] ?? null,
            'status' => $deck->status,
        ];
    }
}