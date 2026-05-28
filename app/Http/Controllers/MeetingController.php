<?php

namespace App\Http\Controllers;

use App\Events\MeetingScheduled;
use App\Models\Meeting;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMeetingRequest;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $upcoming = Meeting::with(['scheduler', 'invitee', 'startup'])
            ->where(function ($q) use ($userId) {
                $q->where('scheduler_id', $userId)->orWhere('invitee_id', $userId);
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->paginate(10, ['*'], 'upcoming_page');

        $past = Meeting::with(['scheduler', 'invitee', 'startup'])
            ->where(function ($q) use ($userId) {
                $q->where('scheduler_id', $userId)->orWhere('invitee_id', $userId);
            })
            ->where(function ($q) {
                $q->whereIn('status', ['declined', 'cancelled'])
                  ->orWhere('scheduled_at', '<', now());
            })
            ->orderByDesc('scheduled_at')
            ->paginate(10, ['*'], 'past_page');

        $meetings = Meeting::with(['scheduler', 'invitee', 'startup'])
            ->where(function ($q) use ($userId) {
                $q->where('scheduler_id', $userId)->orWhere('invitee_id', $userId);
            })
            ->latest('scheduled_at')
            ->paginate(10, ['*'], 'all_page');

        return view('meetings.index', compact('upcoming', 'past', 'meetings'));
    }

    public function show(Meeting $meeting)
    {
        $this->authorize('view', $meeting);

        $meeting->load(['scheduler', 'invitee', 'startup']);
        return view('meetings.show', compact('meeting'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Meeting::class);

        $users = User::where('id', '!=', auth()->id())->get()->groupBy('role');
        $startupId = $request->input('startup_id');
        $startup = $startupId ? Startup::find($startupId) : null;
        $invitee = $request->filled('invitee_id')
            ? User::find($request->invitee_id)
            : null;

        return view('meetings.create', compact('users', 'startupId', 'startup', 'invitee'));
    }

    public function store(StoreMeetingRequest $request)
    {
        $data = $request->validated();
        $data['scheduler_id'] = auth()->id();
        $data['status'] = 'pending';

        $meeting = Meeting::create($data);

        event(new MeetingScheduled($meeting));

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting scheduled successfully.');
    }

    public function edit(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        $users = User::where('id', '!=', auth()->id())->get();
        return view('meetings.edit', compact('meeting', 'users'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $meeting->update($request->only(['title', 'scheduled_at', 'location', 'notes']));

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting updated.');
    }

    public function accept(Meeting $meeting)
    {
        $this->authorize('updateStatus', $meeting);

        $meeting->update(['status' => 'accepted']);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting accepted.');
    }

    public function decline(Meeting $meeting)
    {
        $this->authorize('updateStatus', $meeting);

        $meeting->update(['status' => 'declined']);

        return redirect()->route('meetings.show', $meeting)
            ->with('status', 'Meeting declined.');
    }

    public function cancel(Meeting $meeting)
    {
        $this->authorize('updateStatus', $meeting);

        $meeting->update(['status' => 'cancelled']);

        return redirect()->route('meetings.index')
            ->with('status', 'Meeting cancelled.');
    }

    public function destroy(Meeting $meeting)
    {
        $this->authorize('delete', $meeting);
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Meeting deleted.');
    }
}