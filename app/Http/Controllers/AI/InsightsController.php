<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\PitchDeck;
use App\Models\Startup;
use App\Services\AIService;
use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function index(Request $request)
    {
        $trending = Startup::with('founder')
            ->trending()
            ->limit(10)
            ->get();

        $byIndustry = Startup::selectRaw('industry, count(*) as total, avg(amount_raised) as avg_raised')
            ->whereNotNull('industry')
            ->groupBy('industry')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $topIndustry = $byIndustry->first();

        $stats = [
            'total_analyzed' => PitchDeck::whereIn('status', ['analyzed', 'final'])->count(),
            'avg_score' => (int) (PitchDeck::where('status', 'analyzed')->avg('ai_score') ?: 0),
            'top_industry' => $topIndustry?->industry ?? 'N/A',
            'total_startups' => Startup::count(),
            'total_investments' => Investment::count(),
        ];

        $comparisons = $this->buildComparisons();

        $recommendations = $this->aiService->generateInsightRecommendations([
            'stats' => $stats,
            'industries' => $byIndustry->pluck('industry')->take(5)->values()->all(),
            'top_startup' => $trending->first()?->name,
        ]);

        return view('ai.insights', compact('trending', 'byIndustry', 'stats', 'comparisons', 'recommendations'));
    }

    protected function buildComparisons(): array
    {
        $groups = Startup::whereNotNull('industry')
            ->with('founder')
            ->get()
            ->groupBy('industry')
            ->filter(fn ($items) => $items->count() >= 2);

        $comparisons = [];

        foreach ($groups->take(3) as $industry => $startups) {
            $ranked = $startups->sortByDesc(fn ($s) => ($s->amount_raised ?? 0) + ($s->investor_count * 1000))->values();

            $comparisons[] = [
                'industry' => $industry,
                'leader' => $ranked->first(),
                'challenger' => $ranked->get(1),
                'metric' => 'Funding + investor engagement',
            ];
        }

        return $comparisons;
    }
}
