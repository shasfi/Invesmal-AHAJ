<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_startups' => Startup::count(),
            'total_investments' => \App\Models\Investment::count(),
        ];

        $pendingCount = User::where('is_verified', false)->count() + Startup::where('is_verified', false)->count();

        $unverifiedUsers = User::where('is_verified', false)->latest()->get();
        $unverifiedStartups = Startup::where('is_verified', false)->with('founder')->latest()->get();

        $recentActivities = \App\Models\ActivityLog::with('user')->latest()->take(10)->get();

        // Chart data
        $userGrowthData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $startupStageData = Startup::selectRaw('stage, COUNT(*) as count')
            ->groupBy('stage')
            ->get();

        $roleDistributionData = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        $investmentTrendData = \App\Models\Investment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Investor-Student interaction tracking
        $recentInteractions = \App\Models\Investment::with(['investor', 'startup.founder'])
            ->latest()
            ->take(10)
            ->get();

        $totalInvestmentAmount = \App\Models\Investment::where('status', 'approved')->sum('amount');

        $activeInvestors = User::where('role', 'investor')
            ->whereHas('investments', fn($q) => $q->where('status', 'approved'))
            ->count();

        $activeFounders = User::where('role', 'student_founder')
            ->whereHas('startups', fn($q) => $q->whereHas('investments', fn($iq) => $iq->where('status', 'approved')))
            ->count();

        return view('admin.dashboard', compact(
            'stats',
            'pendingCount',
            'unverifiedUsers',
            'unverifiedStartups',
            'recentActivities',
            'userGrowthData',
            'startupStageData',
            'roleDistributionData',
            'investmentTrendData',
            'recentInteractions',
            'totalInvestmentAmount',
            'activeInvestors',
            'activeFounders'
        ));
    }
}
