<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Meeting;
use App\Models\PitchDeck;
use App\Models\Startup;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    /**
     * Show the role-based dashboard with contextual data.
     */
    public function __invoke(): View
    {
        $user = Auth::user();
        $role = $user->role;

        $data = [
            'role' => $role,
            'user' => $user,
            'unreadNotificationCount' => $this->notificationService->unreadCount($user),
        ];

        switch ($role) {
            case 'admin':
                $data = array_merge($data, $this->getAdminData());
                break;
            case 'investor':
                $data = array_merge($data, $this->getInvestorData());
                break;
            case 'mentor':
                $data = array_merge($data, $this->getMentorData());
                break;
            case 'student_founder':
            default:
                $data = array_merge($data, $this->getFounderData());
                break;
        }

        return view('dashboard.index', $data);
    }

    private function getAdminData(): array
    {
        $userStats = User::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN role = 'student_founder' THEN 1 ELSE 0 END) as founders,
            SUM(CASE WHEN role = 'investor' THEN 1 ELSE 0 END) as investors,
            SUM(CASE WHEN role = 'mentor' THEN 1 ELSE 0 END) as mentors
        ")->first();

        $stageStats = Startup::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stage = 'idea' THEN 1 ELSE 0 END) as idea,
            SUM(CASE WHEN stage = 'mvp' THEN 1 ELSE 0 END) as mvp,
            SUM(CASE WHEN stage = 'funded' THEN 1 ELSE 0 END) as funded
        ")->first();

        $unverifiedUsers = User::where('is_verified', false)->latest()->get();
        $unverifiedStartups = Startup::where('is_verified', false)->with('founder')->latest()->get();

        return [
            'totalUsers' => $userStats->total,
            'totalStartups' => $stageStats->total,
            'pendingVerifications' => $userStats->pending,
            'foundersCount' => $userStats->founders,
            'investorsCount' => $userStats->investors,
            'mentorsCount' => $userStats->mentors,
            'ideaStageCount' => $stageStats->idea,
            'mvpStageCount' => $stageStats->mvp,
            'fundedStageCount' => $stageStats->funded,
            'recentUsers' => User::latest()->take(5)->get(),
            'recentStartups' => Startup::with('founder')->latest()->take(5)->get(),
            'unverifiedUsers' => $unverifiedUsers,
            'unverifiedStartups' => $unverifiedStartups,
        ];
    }

    private function getFounderData(): array
    {
        $user = Auth::user();
        $myStartups = $user->startups()->with('founder')->latest()->get();

        $startupIds = $myStartups->pluck('id');

        return [
            'myStartups' => $myStartups,
            'myStartupsCount' => $myStartups->count(),
            'avgProgress' => $myStartups->isEmpty() ? 0 : round($myStartups->avg(fn($s) => $s->calculateProgressScore())),
            'ideaCount' => $myStartups->where('stage', 'idea')->count(),
            'mvpCount' => $myStartups->where('stage', 'mvp')->count(),
            'fundedCount' => $myStartups->where('stage', 'funded')->count(),
            'pitchDeckCount' => PitchDeck::where('user_id', $user->id)->count(),
            'analyzedDecks' => PitchDeck::where('user_id', $user->id)->whereIn('status', ['analyzed', 'final'])->count(),
            'pendingInvestmentOffers' => Investment::with(['investor', 'startup'])
                ->whereIn('startup_id', $startupIds)
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    private function getInvestorData(): array
    {
        $stageStats = Startup::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stage = 'idea' THEN 1 ELSE 0 END) as idea,
            SUM(CASE WHEN stage = 'mvp' THEN 1 ELSE 0 END) as mvp,
            SUM(CASE WHEN stage = 'funded' THEN 1 ELSE 0 END) as funded
        ")->first();

        $user = Auth::user();
        $myInvestmentsQuery = Investment::where('investor_id', $user->id);

        return [
            'totalStartups' => $stageStats->total,
            'ideaStartups' => $stageStats->idea,
            'mvpStartups' => $stageStats->mvp,
            'fundedStartups' => $stageStats->funded,
            'myInvestments' => (clone $myInvestmentsQuery)->count(),
            'pendingInvestments' => (clone $myInvestmentsQuery)->where('status', 'pending')->count(),
            'approvedInvestments' => (clone $myInvestmentsQuery)->where('status', 'approved')->count(),
            'totalInvested' => (clone $myInvestmentsQuery)->where('status', 'approved')->sum('amount') ?: 0,
            'recentInvestments' => Investment::with(['startup.founder'])
                ->where('investor_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'startupsByIndustry' => Startup::groupByIndustry(
                Startup::with('founder')->orderBy('name')->take(24)->get()
            ),
            'topIndustries' => Startup::selectRaw('industry, COUNT(*) as count')
                ->groupBy('industry')
                ->orderByDesc('count')
                ->take(4)
                ->get(),
        ];
    }

    private function getMentorData(): array
    {
        $stageStats = Startup::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stage = 'idea' THEN 1 ELSE 0 END) as idea,
            SUM(CASE WHEN stage = 'mvp' THEN 1 ELSE 0 END) as mvp
        ")->first();

        return [
            'totalStartups' => $stageStats->total,
            'totalFounders' => User::where('role', 'student_founder')->count(),
            'ideaStartups' => $stageStats->idea,
            'mvpStartups' => $stageStats->mvp,
            'upcomingMeetings' => Meeting::where(function ($q) {
                $q->where('invitee_id', Auth::id())->orWhere('scheduler_id', Auth::id());
            })->where('scheduled_at', '>=', now())->count(),
            'startupsByIndustry' => Startup::groupByIndustry(
                Startup::with('founder')->orderBy('name')->get()
            ),
        ];
    }
}
