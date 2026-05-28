<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\Startup;
use App\Models\User;

class MeetingService
{
    public function __construct(
        private NotificationService $notification,
    ) {}

    public function schedule(User $scheduler, User $invitee, array $data): Meeting
    {
        $meeting = Meeting::create([
            'scheduler_id' => $scheduler->id,
            'invitee_id' => $invitee->id,
            'startup_id' => $data['startup_id'] ?? null,
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
            'scheduled_at' => $data['scheduled_at'],
            'status' => 'pending',
            'location' => $data['location'] ?? null,
        ]);

        $this->notification->notify(
            $invitee,
            'info',
            'Meeting Request',
            "{$scheduler->name} wants to schedule: {$data['title']}",
            route('meetings.show', $meeting)
        );

        return $meeting;
    }

    public function accept(Meeting $meeting, User $actor): void
    {
        $meeting->update(['status' => 'accepted']);

        $otherUser = $actor->id === $meeting->scheduler_id ? $meeting->invitee : $meeting->scheduler;
        $this->notification->notify(
            $otherUser,
            'success',
            'Meeting Accepted',
            "{$actor->name} accepted: {$meeting->title}",
            route('meetings.show', $meeting)
        );
    }

    public function decline(Meeting $meeting, User $actor): void
    {
        $meeting->update(['status' => 'declined']);

        $otherUser = $actor->id === $meeting->scheduler_id ? $meeting->invitee : $meeting->scheduler;
        $this->notification->notify(
            $otherUser,
            'warning',
            'Meeting Declined',
            "{$actor->name} declined: {$meeting->title}",
            route('meetings.show', $meeting)
        );
    }

    public function cancel(Meeting $meeting, User $actor): void
    {
        $meeting->update(['status' => 'cancelled']);

        // Notify the other participant
        $otherUser = $actor->id === $meeting->scheduler_id ? $meeting->invitee : $meeting->scheduler;
        $this->notification->notify(
            $otherUser,
            'warning',
            'Meeting Cancelled',
            "{$actor->name} cancelled: {$meeting->title}",
            route('meetings.show', $meeting)
        );
    }

    public function getUserMeetings(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Meeting::where('scheduler_id', $user->id)
            ->orWhere('invitee_id', $user->id)
            ->with(['scheduler', 'invitee', 'startup'])
            ->orderByDesc('scheduled_at')
            ->get();
    }

    public function getUpcomingMeetings(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Meeting::where(function ($q) use ($user) {
            $q->where('scheduler_id', $user->id)->orWhere('invitee_id', $user->id);
        })
            ->upcoming()
            ->with(['scheduler', 'invitee', 'startup'])
            ->orderBy('scheduled_at')
            ->get();
    }
}