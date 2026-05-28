<?php

namespace App\Listeners;

use App\Jobs\ProcessSentimentJob;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Events\UserRegistered;
use App\Events\StartupCreated;
use App\Events\StartupVerified;
use App\Events\InvestmentRequested;
use App\Events\InvestmentStatusChanged;
use App\Events\MeetingScheduled;
use App\Events\MessageSent;
use App\Events\DocumentUploaded;

class SendAppNotification
{
    public function handle($event): void
    {
        // Trigger sentiment analysis on new messages
        if ($event instanceof MessageSent) {
            ProcessSentimentJob::dispatch($event->message->conversation);
        }

        [$userIds, $type, $data] = $this->resolveNotification($event);

        foreach ($userIds as $userId) {
            if (!$userId) {
                continue;
            }

            $recipient = User::find($userId);
            if ($recipient && !NotificationPreference::allowsInApp($recipient, $type)) {
                continue;
            }

            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $data['message'] ?? ucwords(str_replace('_', ' ', $type)),
                'body' => $data['body'] ?? ($data['message'] ?? null),
                'action_url' => $data['action_url'] ?? null,
                'data' => $data,
            ]);
        }
    }

    private function resolveNotification($event): array
    {
        return match (get_class($event)) {
            UserRegistered::class => [
                User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray(),
                'user_registered',
                ['message' => "New user {$event->user->name} registered", 'user_id' => $event->user->id],
            ],
            StartupCreated::class => [
                User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray(),
                'startup_created',
                ['message' => "New startup '{$event->startup->name}' created", 'startup_id' => $event->startup->id],
            ],
            StartupVerified::class => [
                [$event->startup->founder_id],
                'startup_verified',
                ['message' => "Your startup '{$event->startup->name}' has been verified", 'startup_id' => $event->startup->id],
            ],
            InvestmentRequested::class => [
                [$event->investment->startup->founder_id],
                'investment_requested',
                ['message' => "New investment interest for your startup", 'investment_id' => $event->investment->id],
            ],
            InvestmentStatusChanged::class => [
                [$event->investment->investor_id],
                'investment_status_changed',
                ['message' => "Investment status updated to {$event->investment->status}", 'investment_id' => $event->investment->id],
            ],
            MeetingScheduled::class => [
                [$event->meeting->invitee_id],
                'meeting_scheduled',
                [
                    'message' => "New meeting scheduled: {$event->meeting->title}",
                    'meeting_id' => $event->meeting->id,
                    'action_url' => route('meetings.show', $event->meeting),
                ],
            ],
            MessageSent::class => [
                $event->message->conversation->participants()
                    ->where('users.id', '!=', $event->message->sender_id)
                    ->pluck('users.id')
                    ->toArray(),
                'new_message',
                [
                    'message' => 'New message received',
                    'conversation_id' => $event->message->conversation_id,
                    'action_url' => route('conversations.show', $event->message->conversation_id),
                ],
            ],
            DocumentUploaded::class => [
                User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray(),
                'document_uploaded',
                ['message' => "Document '{$event->document->name}' uploaded", 'document_id' => $event->document->id],
            ],
            default => [[], '', []],
        };
    }
}