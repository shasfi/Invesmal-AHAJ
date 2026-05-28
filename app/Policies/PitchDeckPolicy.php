<?php

namespace App\Policies;

use App\Models\PitchDeck;
use App\Models\User;

class PitchDeckPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PitchDeck $pitchDeck): bool
    {
        return $user->id === $pitchDeck->user_id
            || $user->role === User::ROLE_ADMIN
            || in_array($pitchDeck->status, ['analyzed', 'final']);
    }

    public function update(User $user, PitchDeck $pitchDeck): bool
    {
        return $user->id === $pitchDeck->user_id
            || $user->role === User::ROLE_ADMIN;
    }

    public function delete(User $user, PitchDeck $pitchDeck): bool
    {
        return $user->id === $pitchDeck->user_id
            || $user->role === User::ROLE_ADMIN;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [
            User::ROLE_STUDENT_FOUNDER,
            User::ROLE_ADMIN,
        ], true);
    }

    public function generate(User $user): bool
    {
        return in_array($user->role, [
            User::ROLE_STUDENT_FOUNDER,
            User::ROLE_ADMIN,
        ], true);
    }

    public function analyze(User $user, PitchDeck $pitchDeck): bool
    {
        return $user->id === $pitchDeck->user_id
            || $user->role === User::ROLE_ADMIN;
    }
}