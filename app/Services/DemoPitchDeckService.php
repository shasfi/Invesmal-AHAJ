<?php

namespace App\Services;

/**
 * Offline demo pitch deck content when OpenAI is not configured (FYP / local dev).
 */
class DemoPitchDeckService
{
    public function generate(string $startupDescription, ?string $title = null): array
    {
        $excerpt = \Illuminate\Support\Str::limit(trim($startupDescription), 280);
        $name = $title ?: 'Your Startup';

        $sections = [
            ['id' => 'problem', 'title' => 'Problem', 'content' => "Many stakeholders face friction in the space addressed by {$name}. {$excerpt}"],
            ['id' => 'solution', 'title' => 'Solution', 'content' => "{$name} delivers a focused product that removes key pain points described in your plan and improves outcomes for target users."],
            ['id' => 'market', 'title' => 'Market Opportunity', 'content' => 'The addressable market is growing with clear demand from early adopters. Regional expansion and digital channels offer scalable reach.'],
            ['id' => 'product', 'title' => 'Product & Technology', 'content' => 'Core features are built for reliability and ease of use, with a roadmap for analytics, integrations, and mobile access.'],
            ['id' => 'traction', 'title' => 'Traction & Milestones', 'content' => 'Early pilots and user feedback validate problem–solution fit. Next milestones: paid pilots, partnerships, and revenue targets.'],
            ['id' => 'business_model', 'title' => 'Business Model', 'content' => 'Revenue via subscriptions and/or transaction fees with strong unit economics as volume scales.'],
            ['id' => 'competition', 'title' => 'Competitive Landscape', 'content' => 'Incumbents are slow or generic; our differentiation is speed, local insight, and founder-led customer success.'],
            ['id' => 'team', 'title' => 'Team', 'content' => 'Founders combine domain expertise and execution ability; advisors fill gaps in finance, legal, and go-to-market.'],
            ['id' => 'financials', 'title' => 'Financial Projections', 'content' => 'Conservative 3-year projections assume phased hiring and marketing spend aligned with funding rounds.'],
            ['id' => 'ask', 'title' => 'The Ask', 'content' => 'Seeking seed capital to accelerate product, sales, and key hires with clear milestones for the next 12–18 months.'],
        ];

        return [
            'sections' => $sections,
            'executive_summary' => "{$name}: {$excerpt}",
            'tagline' => "{$name} — building the future of your market.",
            '_demo_mode' => true,
        ];
    }

    public function analyze(string $content): array
    {
        $hash = crc32($content);
        $base = 58 + ($hash % 22);

        $categories = [
            'clarity' => ['score' => min(10, 6 + ($hash % 4)), 'comment' => 'Structure is readable; add more data points in each slide.'],
            'problem_statement' => ['score' => 7, 'comment' => 'Problem is stated; quantify impact with numbers or customer quotes.'],
            'solution_fit' => ['score' => 6 + ($hash % 3), 'comment' => 'Solution aligns with the problem; show product screenshots or demo.'],
            'market_opportunity' => ['score' => 7, 'comment' => 'Market size mentioned; cite TAM/SAM/SOM sources.'],
            'business_model' => ['score' => 6, 'comment' => 'Revenue model present; clarify pricing and margins.'],
            'competitive_advantage' => ['score' => 6 + ($hash % 2), 'comment' => 'Differentiation noted; strengthen moat (IP, network, data).'],
            'team_strength' => ['score' => 5 + ($hash % 3), 'comment' => 'Team section needs advisor logos and relevant experience.'],
            'financial_projections' => ['score' => 5, 'comment' => 'Add 3-year forecast and key assumptions.'],
            'ask_clarity' => ['score' => 6, 'comment' => 'Funding ask should state amount, use of funds, and runway.'],
        ];

        return [
            'overall_score' => $base,
            'verdict' => 'Demo analysis — add OPENAI_API_KEY in .env for full AI feedback.',
            'strengths' => ['Clear narrative flow', 'Covers standard investor sections', 'Good foundation for iteration'],
            'weaknesses' => ['Limited quantitative data', 'Team and financials need depth'],
            'categories' => $categories,
            'key_improvements' => [
                'Add metrics: users, revenue, growth rate',
                'Include customer testimonials or LOIs',
                'Tighten the ask slide with exact funding amount',
            ],
            'investor_readiness' => $base >= 70 ? 'Approaching' : 'Not Ready',
            '_demo_mode' => true,
        ];
    }
}
