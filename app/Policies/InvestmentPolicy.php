<?php

namespace App\Policies;

use App\Models\Investment;
use App\Models\User;

class InvestmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            User::ROLE_INVESTOR,
            User::ROLE_STUDENT_FOUNDER,
            User::ROLE_ADMIN,
        ], true);
    }

    public function view(User $user, Investment $investment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        if ($user->id === $investment->investor_id) {
            return true;
        }

        return $user->id === $investment->startup->founder_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_INVESTOR, User::ROLE_ADMIN], true);
    }

    public function update(User $user, Investment $investment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $user->id === $investment->investor_id;
    }

    public function approve(User $user, Investment $investment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $user->id === $investment->startup->founder_id;
    }

    public function delete(User $user, Investment $investment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $user->id === $investment->investor_id;
    }
}
