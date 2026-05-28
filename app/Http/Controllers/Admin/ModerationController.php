<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function index()
    {
        $startups = Startup::with('founder')->latest()->paginate(20);
        return view('admin.dashboard', compact('startups'));
    }

    public function flag(Request $request, Startup $startup)
    {
        $startup->update([
            'is_flagged' => true,
            'flag_reason' => $request->input('flag_reason', 'Flagged by administrator'),
        ]);

        return back()->with('success', 'Startup flagged.');
    }

    public function unflag(Startup $startup)
    {
        $startup->update(['is_flagged' => false]);
        return back()->with('success', 'Startup unflagged.');
    }
}