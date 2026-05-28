<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ConversationService
{
    public function __construct(
        private NotificationService $notification,
    ) {}

    public function findOrCreateDirect(User $userA, User $userB, ?string $subject = null): Conversation
    {
        // Look for existing direct conversation between these two users
        $existing = Conversation::where(function ($q) {
                $q->where('type', 'direct')->orWhereNull('type');
            })
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userA->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userB->id))
            ->has('participants', '=', 2)
            ->first();

        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::create([
            'type' => 'direct',
            'subject' => $subject ?? "{$userA->name} ↔ {$userB->name}",
        ]);

        $conversation->participants()->attach([$userA->id, $userB->id]);

        return $conversation;
    }

    public function getUserConversations(User $user): Collection
    {
        return Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->with(['latestMessage.sender', 'participants'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->take(1)
            )
            ->get()
            ->map(function ($conversation) use ($user) {
                $conversation->unread_count = Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->count();
                return $conversation;
            });
    }

    public function getMessages(Conversation $conversation, int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function sendMessage(Conversation $conversation, User $sender, string $body): Message
    {
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'body' => $body,
        ]);

        // Update participant's last_read_at for the sender
        $conversation->participants()->updateExistingPivot($sender->id, [
            'last_read_at' => now(),
        ]);

        // Notify other participants
        $otherUsers = $conversation->participants()->where('user_id', '!=', $sender->id)->get();
        foreach ($otherUsers as $user) {
            $this->notification->notify(
                $user,
                'info',
                'New Message',
                "{$sender->name}: " . \Illuminate\Support\Str::limit($body, 100),
                route('conversations.show', $conversation)
            );
        }

        return $message;
    }

    public function markRead(Conversation $conversation, User $user): void
    {
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }
}