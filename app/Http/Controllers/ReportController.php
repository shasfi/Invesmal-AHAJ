<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\Investment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Meeting;
use App\Models\PitchDeck;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === User::ROLE_ADMIN) {
            return $this->adminReport();
        }

        if ($user->role === User::ROLE_INVESTOR) {
            return $this->investorReport($user);
        }

        if ($user->role === User::ROLE_STUDENT_FOUNDER) {
            return $this->founderReport($user);
        }

        abort(403, 'Reports are not available for your role.');
    }

    protected function adminReport()
    {
        $this->authorize('viewAny', User::class);

        $stats = [
            'total_users' => User::count(),
            'total_startups' => Startup::count(),
            'total_investments' => Investment::count(),
            'total_meetings' => Meeting::count(),
            'total_raised' => Startup::sum('amount_raised') ?: 0,
            'avg_readiness' => PitchDeck::where('status', 'analyzed')->avg('ai_score') ?: 0,
            'by_stage' => Startup::selectRaw('stage, count(*) as count')->groupBy('stage')->pluck('count', 'stage'),
            'by_industry' => Startup::selectRaw('industry, count(*) as count')->whereNotNull('industry')->groupBy('industry')->orderByDesc('count')->limit(5)->pluck('count', 'industry'),
            'investments_by_status' => Investment::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'monthly_registrations' => User::selectRaw("strftime('%Y-%m', created_at) as month, count(*) as count")->groupBy('month')->orderBy('month')->limit(12)->pluck('count', 'month'),
        ];

        $trendingStartups = Startup::with('founder')->trending()->limit(5)->get();
        $recentActivity = ActivityLog::with('user')->latest()->limit(10)->get();

        return view('reports.index', compact('stats', 'trendingStartups', 'recentActivity'));
    }

    protected function investorReport(User $user)
    {
        $investments = Investment::with('startup')
            ->where('investor_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total_investments' => $investments->count(),
            'pending' => $investments->where('status', 'pending')->count(),
            'approved' => $investments->where('status', 'approved')->count(),
            'rejected' => $investments->where('status', 'rejected')->count(),
            'total_committed' => $investments->where('status', 'approved')->sum('amount'),
        ];

        return view('reports.investor', compact('stats', 'investments'));
    }

    protected function founderReport(User $user)
    {
        $startups = $user->startups()->with('investments')->get();

        $stats = [
            'total_startups' => $startups->count(),
            'verified' => $startups->where('is_verified', true)->count(),
            'total_raised' => $startups->sum('amount_raised'),
            'pending_investments' => Investment::whereIn('startup_id', $startups->pluck('id'))->where('status', 'pending')->count(),
            'approved_investments' => Investment::whereIn('startup_id', $startups->pluck('id'))->where('status', 'approved')->count(),
        ];

        return view('reports.founder', compact('stats', 'startups'));
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $data = match ($user->role) {
            User::ROLE_ADMIN => [
                'title' => 'Platform Report',
                'total_users' => User::count(),
                'total_startups' => Startup::count(),
                'total_investments' => Investment::count(),
                'total_raised' => Startup::sum('amount_raised') ?: 0,
                'by_stage' => Startup::selectRaw('stage, count(*) as count')->groupBy('stage')->get(),
            ],
            User::ROLE_INVESTOR => [
                'title' => 'Investor Activity Report',
                'total_investments' => Investment::where('investor_id', $user->id)->count(),
                'approved' => Investment::where('investor_id', $user->id)->where('status', 'approved')->count(),
                'pending' => Investment::where('investor_id', $user->id)->where('status', 'pending')->count(),
                'by_stage' => collect(),
            ],
            User::ROLE_STUDENT_FOUNDER => [
                'title' => 'Founder Performance Report',
                'total_startups' => $user->startups()->count(),
                'total_raised' => $user->startups()->sum('amount_raised'),
                'by_stage' => $user->startups()->selectRaw('stage, count(*) as count')->groupBy('stage')->get(),
            ],
            default => abort(403),
        };

        $html = view('reports.pdf', array_merge($data, [
            'generated_at' => now()->format('F j, Y g:i A'),
            'user' => $user,
        ]))->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="invesmal-report-' . now()->format('Y-m-d') . '.html"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $user = $request->user();

        $startups = match ($user->role) {
            User::ROLE_ADMIN => Startup::with('founder')->get(),
            User::ROLE_STUDENT_FOUNDER => $user->startups()->get(),
            User::ROLE_INVESTOR => Startup::whereIn(
                'id',
                Investment::where('investor_id', $user->id)->pluck('startup_id')
            )->get(),
            default => abort(403),
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invesmal_report_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($startups) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Industry', 'Stage', 'Funding Goal', 'Amount Raised', 'Created At']);
            foreach ($startups as $startup) {
                fputcsv($file, [
                    $startup->name,
                    $startup->industry,
                    $startup->stage,
                    $startup->funding_goal,
                    $startup->amount_raised,
                    $startup->created_at?->toDateString(),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
