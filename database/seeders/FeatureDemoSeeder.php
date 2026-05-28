<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\Investment;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\PitchDeck;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FeatureDemoSeeder extends Seeder
{
    protected array $industries = ['CleanTech', 'AgriTech', 'HealthTech', 'EdTech', 'FinTech', 'Logistics', 'FoodTech', 'AI/ML', 'E-Commerce', 'Cybersecurity'];

    protected array $stages = ['idea', 'mvp', 'funded'];

    public function run(): void
    {
        $this->command?->info('Seeding Invesmal demo data (10+ records per feature)...');

        $users = $this->seedUsers();
        $startups = $this->seedStartups($users, 12);
        $this->seedPitchDecks($users, $startups, 12);
        $this->seedInvestments($users, $startups, 12);
        $this->seedMeetings($users, $startups, 12);
        $this->seedDocuments($users, $startups, 12);
        $conversations = $this->seedConversations($users, 12);
        $this->seedMessages($conversations, 24);
        $this->seedNotifications($users, 12);
        $this->seedActivityLogs($users, 12);
        $this->seedNotificationPreferences($users);
        $this->seedAiDemoSamples();

        $this->command?->info('Demo seed complete. Login: any user below with password: password');
        $this->command?->table(['Email', 'Role'], collect($users)->map(fn ($u) => [$u->email, $u->role])->all());
    }

    protected function seedUsers(): array
    {
        $defs = [
            ['admin@invesmal.com', 'Admin User', 'admin', true],
            ['ahmed@invesmal.com', 'Ahmed Khan', 'student_founder', true],
            ['fatima@invesmal.com', 'Fatima Ali', 'student_founder', true],
            ['usman@invesmal.com', 'Usman Raza', 'student_founder', true],
            ['hira@invesmal.com', 'Hira Shah', 'student_founder', true],
            ['zain@invesmal.com', 'Zain Hassan', 'investor', true],
            ['sara@invesmal.com', 'Sara Malik', 'investor', true],
            ['omar@invesmal.com', 'Omar Tariq', 'investor', true],
            ['bilal@invesmal.com', 'Dr. Bilal', 'mentor', true],
            ['ayesha@invesmal.com', 'Ayesha Noor', 'mentor', true],
        ];

        $users = [];
        foreach ($defs as $i => [$email, $name, $role, $verified]) {
            $users[] = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'is_verified' => $verified,
                    'email_verified_at' => now(),
                    'bio' => "Demo {$role} account #" . ($i + 1),
                    'university' => in_array($role, ['student_founder'], true) ? 'NUST' : null,
                ]
            );
        }

        return $users;
    }

    protected function seedStartups(array $users, int $min): array
    {
        if (Startup::count() >= $min) {
            return Startup::limit($min)->get()->all();
        }

        $founders = collect($users)->where('role', 'student_founder')->values();
        $names = ['EcoCharge', 'FarmLink', 'MediConnect', 'EduBridge', 'PaySwift', 'LogiTrack', 'FoodHive', 'MindAI', 'ShopLocal', 'SecureNet', 'GreenGrid', 'CarePlus'];
        $startups = Startup::all()->all();

        for ($i = Startup::count(); $i < $min; $i++) {
            $founder = $founders[$i % $founders->count()];
            $startups[] = Startup::create([
                'founder_id' => $founder->id,
                'name' => $names[$i % count($names)] . ($i > count($names) - 1 ? " {$i}" : ''),
                'description' => "Innovative {$this->industries[$i % 10]} startup solving real market problems in Pakistan.",
                'stage' => $this->stages[$i % 3],
                'industry' => $this->industries[$i % 10],
                'funding_goal' => 25000 + ($i * 5000),
                'amount_raised' => $i % 3 === 2 ? 20000 + ($i * 3000) : ($i % 2) * 8000,
                'team_size' => 2 + ($i % 6),
                'is_verified' => $i % 4 !== 3,
                'problem' => 'Market gap identified in sector.',
                'solution' => 'Technology-driven scalable solution.',
                'mission' => 'Empower users through innovation.',
            ]);
        }

        return array_slice($startups, 0, $min);
    }

    protected function seedPitchDecks(array $users, array $startups, int $min): void
    {
        if (PitchDeck::count() >= $min) {
            return;
        }

        $founders = collect($users)->where('role', 'student_founder')->values();
        $sampleContent = [
            'tagline' => 'Building the future of investment-ready startups.',
            'executive_summary' => 'We address a large market with a scalable product and strong team execution.',
            'sections' => [
                ['id' => 'problem', 'title' => 'Problem', 'content' => 'Customers face high friction and cost.'],
                ['id' => 'solution', 'title' => 'Solution', 'content' => 'Our platform reduces time and cost by 40%.'],
                ['id' => 'market', 'title' => 'Market', 'content' => 'TAM $500M in Pakistan with 25% CAGR.'],
                ['id' => 'ask', 'title' => 'The Ask', 'content' => 'Raising seed round for product and GTM.'],
            ],
        ];

        for ($i = PitchDeck::count(); $i < $min; $i++) {
            $founder = $founders[$i % $founders->count()];
            $status = match ($i % 4) {
                0 => 'draft',
                1 => 'generated',
                2 => 'analyzed',
                default => 'final',
            };

            PitchDeck::create([
                'user_id' => $founder->id,
                'title' => "Pitch Deck " . ($i + 1) . " — Demo Startup",
                'startup_description' => 'Demo startup description for AI pitch deck generation and analysis.',
                'content_json' => $sampleContent,
                'status' => $status,
                'ai_score' => in_array($status, ['analyzed', 'final'], true) ? 55 + ($i % 40) : null,
                'ai_analysis' => in_array($status, ['analyzed', 'final'], true) ? [
                    'overall_score' => 55 + ($i % 40),
                    'verdict' => 'Promising early-stage opportunity with clear improvement areas.',
                    'strengths' => ['Clear problem', 'Strong team'],
                    'weaknesses' => ['Financials need detail'],
                    'investor_readiness' => 'Approaching',
                ] : null,
            ]);
        }
    }

    protected function seedInvestments(array $users, array $startups, int $min): void
    {
        if (Investment::count() >= $min) {
            return;
        }

        $investors = collect($users)->where('role', 'investor')->values();
        $statuses = ['pending', 'approved', 'rejected'];

        $created = 0;
        $si = 0;
        while (Investment::count() < $min && $created < 50) {
            $investor = $investors[$created % $investors->count()];
            $startup = $startups[$si % count($startups)] ?? Startup::first();
            if (!$startup) {
                break;
            }

            $exists = Investment::where('investor_id', $investor->id)
                ->where('startup_id', $startup->id)
                ->exists();

            $si++;

            if ($exists) {
                continue;
            }

            Investment::create([
                'investor_id' => $investor->id,
                'startup_id' => $startup->id,
                'amount' => 5000 + ($created * 2500),
                'status' => $statuses[$created % 3],
                'message' => 'Demo investment interest #' . ($created + 1),
            ]);

            $created++;
        }
    }

    protected function seedMeetings(array $users, array $startups, int $min): void
    {
        if (Meeting::count() >= $min) {
            return;
        }

        $statuses = ['pending', 'accepted', 'declined', 'cancelled'];
        $roles = collect($users)->filter(fn ($u) => in_array($u->role, ['student_founder', 'investor', 'mentor'], true))->values();

        for ($i = Meeting::count(); $i < $min; $i++) {
            $scheduler = $roles[$i % $roles->count()];
            $invitee = $roles[($i + 1) % $roles->count()];
            if ($scheduler->id === $invitee->id) {
                $invitee = $roles[($i + 2) % $roles->count()];
            }

            $startup = $startups[$i % count($startups)] ?? null;

            Meeting::create([
                'scheduler_id' => $scheduler->id,
                'invitee_id' => $invitee->id,
                'startup_id' => $startup?->id,
                'title' => 'Demo Meeting ' . ($i + 1),
                'notes' => 'Discussion about funding, mentorship, or product roadmap.',
                'scheduled_at' => now()->addDays($i + 1)->setHour(10 + ($i % 6)),
                'status' => $statuses[$i % 4],
                'location' => $i % 2 ? 'Zoom' : 'Campus Incubator',
            ]);
        }
    }

    protected function seedDocuments(array $users, array $startups, int $min): void
    {
        if (Document::count() >= $min) {
            return;
        }

        $founders = collect($users)->where('role', 'student_founder')->values();
        $types = ['pitch_deck', 'business_plan', 'other'];

        for ($i = Document::count(); $i < $min; $i++) {
            $founder = $founders[$i % $founders->count()];
            $startup = $startups[$i % count($startups)] ?? null;
            $type = $types[$i % 3];

            Document::create([
                'user_id' => $founder->id,
                'startup_id' => $startup?->id,
                'type' => $type,
                'filename' => "demo_doc_{$i}.pdf",
                'original_name' => "Demo_{$type}_" . ($i + 1) . '.pdf',
                'path' => "documents/demo/demo_{$i}.pdf",
                'version' => ($i % 3) + 1,
                'size' => 102400 * ($i + 1),
                'mime_type' => 'application/pdf',
                'description' => "Demo document for {$type} module testing.",
            ]);
        }
    }

    protected function seedConversations(array $users, int $min): array
    {
        Conversation::whereNull('type')->update(['type' => 'direct']);

        if (Conversation::count() >= $min) {
            return Conversation::limit($min)->get()->all();
        }

        $conversations = Conversation::all()->all();
        $pool = collect($users)->filter(fn ($u) => $u->role !== 'admin')->values();

        for ($i = Conversation::count(); $i < $min; $i++) {
            $a = $pool[$i % $pool->count()];
            $b = $pool[($i + 1) % $pool->count()];

            $conv = Conversation::create([
                'type' => 'direct',
                'subject' => 'Demo Conversation ' . ($i + 1),
                'sentiment_score' => 40 + ($i % 50),
                'sentiment_label' => ['Positive', 'Neutral', 'Negative'][$i % 3],
                'sentiment_breakdown' => [
                    'positive_percent' => 30 + ($i % 40),
                    'neutral_percent' => 25,
                    'negative_percent' => 15 + ($i % 20),
                    'summary' => 'Demo sentiment analysis for FYP presentation.',
                    'message_count' => 2,
                ],
                'sentiment_analyzed_at' => now(),
            ]);

            DB::table('conversation_participants')->insertOrIgnore([
                ['conversation_id' => $conv->id, 'user_id' => $a->id],
                ['conversation_id' => $conv->id, 'user_id' => $b->id],
            ]);

            $conversations[] = $conv;
        }

        return $conversations;
    }

    protected function seedMessages(array $conversations, int $min): void
    {
        $samples = [
            'Hello! I saw your startup on Invesmal and would like to connect.',
            'Thanks for reaching out. We are currently raising our seed round.',
            'Could you share your pitch deck and latest traction numbers?',
            'Sure — I uploaded the deck under Pitch Decks. ARR is growing 18% month over month.',
            'That sounds promising. Are you open to a 30-minute call this week?',
            'Yes, I scheduled a meeting for Thursday. Looking forward to it!',
            'Great. I also have a few questions about your unit economics.',
            'Happy to walk through that on the call. See you then!',
        ];

        foreach ($conversations as $conv) {
            $existing = Message::where('conversation_id', $conv->id)->count();
            if ($existing >= 6) {
                continue;
            }

            $participants = DB::table('conversation_participants')
                ->where('conversation_id', $conv->id)
                ->pluck('user_id')
                ->values();

            if ($participants->count() < 2) {
                continue;
            }

            for ($m = $existing; $m < 6; $m++) {
                Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id' => $participants[$m % 2],
                    'body' => $samples[$m % count($samples)],
                    'created_at' => now()->subMinutes((6 - $m) * 15),
                    'read_at' => $m < 5 ? now()->subMinutes((5 - $m) * 10) : null,
                ]);
            }
        }

        if (Message::count() < $min) {
            $this->command?->warn('Message count below target; re-run after clearing messages or use migrate:fresh --seed.');
        }
    }

    protected function seedNotifications(array $users, int $min): void
    {
        if (Notification::count() >= $min) {
            return;
        }

        $types = ['meeting_scheduled', 'investment_requested', 'investment_status_changed', 'new_message', 'startup_verified'];

        for ($i = Notification::count(); $i < $min; $i++) {
            $user = $users[$i % count($users)];

            Notification::create([
                'user_id' => $user->id,
                'type' => $types[$i % count($types)],
                'title' => 'Demo notification ' . ($i + 1),
                'body' => 'This is sample in-app notification data for testing.',
                'data' => ['demo' => true, 'index' => $i + 1],
                'read_at' => $i % 3 === 0 ? now() : null,
            ]);
        }
    }

    protected function seedActivityLogs(array $users, int $min): void
    {
        if (ActivityLog::count() >= $min) {
            return;
        }

        $actions = ['UserRegistered', 'StartupCreated', 'MeetingScheduled', 'InvestmentRequested', 'DocumentUploaded', 'MessageSent'];

        for ($i = ActivityLog::count(); $i < $min; $i++) {
            $user = $users[$i % count($users)];

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $actions[$i % count($actions)],
                'entity_type' => 'demo',
                'entity_id' => $i + 1,
                'changes' => ['demo' => true],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'FeatureDemoSeeder',
            ]);
        }
    }

    protected function seedNotificationPreferences(array $users): void
    {
        foreach ($users as $user) {
            NotificationPreference::firstOrCreate(
                ['user_id' => $user->id],
                NotificationPreference::defaultAttributes()
            );
        }
    }

    protected function seedAiDemoSamples(): void
    {
        $sentimentSummaries = [
            'Positive' => 'Optimistic tone — investor interest, traction shared, meeting scheduled.',
            'Neutral' => 'Professional Q&A about pitch deck, funding stage, and next steps.',
            'Negative' => 'Concerns about valuation and timeline — constructive but cautious tone.',
        ];

        Conversation::withCount('messages')->get()->each(function ($conv, $i) use ($sentimentSummaries) {
            $label = ['Positive', 'Neutral', 'Negative'][$i % 3];
            $positive = match ($label) {
                'Positive' => 55 + ($i % 25),
                'Neutral' => 28 + ($i % 12),
                default => 12 + ($i % 15),
            };
            $negative = match ($label) {
                'Negative' => 38 + ($i % 18),
                'Neutral' => 18 + ($i % 10),
                default => 8 + ($i % 8),
            };
            $neutral = max(5, 100 - $positive - $negative);

            $conv->update([
                'sentiment_score' => match ($label) {
                    'Positive' => 72 + ($i % 22),
                    'Negative' => 28 + ($i % 18),
                    default => 48 + ($i % 12),
                },
                'sentiment_label' => $label,
                'sentiment_breakdown' => [
                    'positive_percent' => $positive,
                    'neutral_percent' => $neutral,
                    'negative_percent' => $negative,
                    'summary' => $sentimentSummaries[$label],
                    'message_count' => $conv->messages_count,
                ],
                'sentiment_analyzed_at' => now(),
            ]);
        });

        $verdicts = ['Strong', 'Promising', 'Solid', 'Investor-ready'];
        PitchDeck::all()->each(function ($deck, $i) use ($verdicts) {
            $score = 62 + ($i % 33);
            $status = in_array($deck->status, ['analyzed', 'final'], true)
                ? $deck->status
                : ($i % 2 === 0 ? 'analyzed' : 'generated');

            if ($status === 'generated' && empty($deck->ai_analysis)) {
                return;
            }

            $deck->update([
                'status' => $status === 'draft' ? 'analyzed' : $status,
                'ai_score' => $score,
                'ai_analysis' => [
                    'overall_score' => $score,
                    'verdict' => $verdicts[$i % 4] . ' pitch — demo AI analysis for FYP presentation.',
                    'strengths' => ['Clear problem statement', 'Scalable business model', 'Strong founder story'],
                    'weaknesses' => ['Financials need more detail', 'Competitive differentiation'],
                    'investor_readiness' => $score >= 80 ? 'Ready' : 'Approaching',
                    'sections_feedback' => [
                        'problem' => 'Pain point is well defined for the target market.',
                        'solution' => 'Product approach is credible and differentiated.',
                        'market' => 'TAM/SAM sizing included with growth assumptions.',
                        'ask' => 'Use of funds and milestones are outlined.',
                    ],
                ],
                'ai_summary' => [
                    'headline' => $deck->title,
                    'elevator_pitch' => 'AI-generated summary: high-growth startup targeting Pakistan market with scalable tech.',
                    'key_metrics' => ['Growth' => '18% MoM', 'Team size' => '5', 'Stage' => 'Seed'],
                ],
            ]);
        });

        $this->command?->info('AI demo samples ready (sentiment + pitch deck analysis).');
    }
}
