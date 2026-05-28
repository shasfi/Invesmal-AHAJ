<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_verified', false)->latest()->get();
        $pendingStartups = Startup::where('is_verified', false)->latest()->get();
        $flaggedStartups = Startup::where('is_flagged', true)->latest()->get();

        return view('admin.dashboard', compact('pendingUsers', 'pendingStartups', 'flaggedStartups'));
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved', 'is_verified' => true]);
        return back()->with('success', 'User approved.');
    }

    public function rejectUser(User $user)
    {
        $user->update(['status' => 'rejected']);
        return back()->with('success', 'User rejected.');
    }

    public function approveStartup(Startup $startup)
    {
        $startup->update([
            'status' => 'approved',
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return back()->with('success', 'Startup approved.');
    }

    public function rejectStartup(Startup $startup)
    {
        $startup->update(['status' => 'rejected']);
        return back()->with('success', 'Startup rejected.');
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $user->update(['status' => $request->status]);
        if ($request->status === 'approved') {
            $user->update(['is_verified' => true]);
        }
        return back()->with('success', 'User status updated.');
    }

    public function updateStartupStatus(Request $request, Startup $startup)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $startup->update(['status' => $request->status]);
        if ($request->status === 'approved') {
            $startup->update([
                'is_verified' => true,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }
        return back()->with('success', 'Startup status updated.');
    }
}