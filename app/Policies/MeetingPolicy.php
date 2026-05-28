<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $meeting->scheduler_id || $user->id === $meeting->invitee_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Meeting $meeting): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $meeting->scheduler_id;
    }

    public function updateStatus(User $user, Meeting $meeting): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $meeting->scheduler_id || $user->id === $meeting->invitee_id;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $meeting->scheduler_id;
    }
}