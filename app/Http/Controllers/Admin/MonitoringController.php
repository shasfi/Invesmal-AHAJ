<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use App\Models\Investment;
use App\Models\Meeting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_startups' => Startup::count(),
            'total_investments' => Investment::count(),
            'total_meetings' => Meeting::count(),
            'founders' => User::where('role', 'student_founder')->count(),
            'investors' => User::where('role', 'investor')->count(),
            'mentors' => User::where('role', 'mentor')->count(),
            'idea_startups' => Startup::where('stage', 'idea')->count(),
            'mvp_startups' => Startup::where('stage', 'mvp')->count(),
            'funded_startups' => Startup::where('stage', 'funded')->count(),
        ];

        $recentActivity = ActivityLog::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }
}