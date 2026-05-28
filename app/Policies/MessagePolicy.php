<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Message $message): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        $conversation = $message->conversation;
        return $conversation->participants()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Message $message): bool
    {
        if ($user->role === User::ROLE_ADMIN) return true;
        return $user->id === $message->sender_id;
    }
}