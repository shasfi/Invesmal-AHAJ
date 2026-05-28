<?php

namespace App\Policies;

use App\Models\Startup;
use App\Models\User;

class StartupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Startup $startup): bool
    {
        if ($startup->is_verified) return true;
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $startup->founder_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_STUDENT_FOUNDER, User::ROLE_ADMIN]);
    }

    public function update(User $user, Startup $startup): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $startup->founder_id;
    }

    public function delete(User $user, Startup $startup): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $startup->founder_id;
    }

    public function verify(User $user, Startup $startup): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MENTOR]);
    }
}