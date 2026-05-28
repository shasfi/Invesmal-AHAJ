<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $document->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function download(User $user, Document $document): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        if ($user->id === $document->user_id) return true;

        $startup = $document->startup;
        if ($startup && $startup->is_verified) return true;

        return false;
    }

    public function delete(User $user, Document $document): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $document->user_id;
    }
}