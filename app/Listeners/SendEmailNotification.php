<?php

namespace App\Listeners;

use App\Events\InvestmentRequested;
use App\Events\InvestmentStatusChanged;
use App\Events\MeetingScheduled;
use App\Events\MessageSent;
use App\Events\StartupVerified;
use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification
{
    public function handle(object $event): void
    {
        $payload = $this->resolveEmailPayload($event);

        if (!$payload) {
            return;
        }

        $user = $payload['user'];

        if (!$user instanceof User || !$user->email) {
            return;
        }

        if (!NotificationPreference::allowsEmail($user, $payload['type'])) {
            return;
        }

        $notification = Notification::where('user_id', $user->id)
            ->where('type', $payload['type'])
            ->where('created_at', '>=', now()->subMinute())
            ->latest()
            ->first();

        if ($notification) {
            $notification->update([
                'body' => $payload['body'],
                'action_url' => $payload['action_url'],
            ]);
        } else {
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $payload['type'],
                'title' => $payload['title'],
                'body' => $payload['body'],
                'action_url' => $payload['action_url'],
                'data' => $payload['data'] ?? null,
            ]);
        }

        try {
            Mail::to($user->email)->queue(new NotificationMail($user, $notification));
        } catch (\Throwable $e) {
            Log::warning('SendEmailNotification: failed to queue email', [
                'user_id' => $user->id,
                'type' => $payload['type'],
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveEmailPayload(object $event): ?array
    {
        return match (get_class($event)) {
            MeetingScheduled::class => $this->meetingPayload($event),
            InvestmentRequested::class => $this->investmentRequestedPayload($event),
            InvestmentStatusChanged::class => $this->investmentStatusPayload($event),
            StartupVerified::class => $this->startupVerifiedPayload($event),
            MessageSent::class => $this->messagePayload($event),
            default => null,
        };
    }

    private function meetingPayload(MeetingScheduled $event): ?array
    {
        $meeting = $event->meeting->loadMissing(['invitee', 'scheduler']);
        $invitee = $meeting->invitee;

        if (!$invitee) {
            return null;
        }

        $when = $meeting->scheduled_at?->format('M d, Y \a\t g:i A') ?? 'TBD';

        return [
            'user' => $invitee,
            'type' => 'meeting_scheduled',
            'title' => "New meeting scheduled: {$meeting->title}",
            'body' => "{$meeting->scheduler?->name} scheduled a meeting with you on {$when}."
                . ($meeting->location ? " Location: {$meeting->location}." : ''),
            'action_url' => route('meetings.show', $meeting),
            'data' => ['meeting_id' => $meeting->id],
        ];
    }

    private function investmentRequestedPayload(InvestmentRequested $event): ?array
    {
        $investment = $event->investment->loadMissing(['startup.founder', 'investor']);
        $founder = $investment->startup?->founder;

        if (!$founder) {
            return null;
        }

        return [
            'user' => $founder,
            'type' => 'investment_requested',
            'title' => 'New investment interest',
            'body' => "{$investment->investor?->name} has shown interest in your startup \"{$investment->startup->name}\".",
            'action_url' => route('investments.show', $investment),
            'data' => ['investment_id' => $investment->id],
        ];
    }

    private function investmentStatusPayload(InvestmentStatusChanged $event): ?array
    {
        $investment = $event->investment->loadMissing(['startup', 'investor']);
        $investor = $investment->investor;

        if (!$investor) {
            return null;
        }

        return [
            'user' => $investor,
            'type' => 'investment_status_changed',
            'title' => 'Investment status updated',
            'body' => "Your investment in \"{$investment->startup->name}\" is now {$investment->status}.",
            'action_url' => route('investments.show', $investment),
            'data' => ['investment_id' => $investment->id],
        ];
    }

    private function startupVerifiedPayload(StartupVerified $event): ?array
    {
        $startup = $event->startup->loadMissing('founder');
        $founder = $startup->founder;

        if (!$founder) {
            return null;
        }

        return [
            'user' => $founder,
            'type' => 'startup_verified',
            'title' => 'Startup verified',
            'body' => "Your startup \"{$startup->name}\" has been verified on Invesmal.",
            'action_url' => route('startups.show', $startup),
            'data' => ['startup_id' => $startup->id],
        ];
    }

    private function messagePayload(MessageSent $event): ?array
    {
        $message = $event->message->loadMissing(['sender', 'conversation.participants']);
        $recipient = $message->conversation
            ->participants()
            ->where('users.id', '!=', $message->sender_id)
            ->first();

        if (!$recipient) {
            return null;
        }

        return [
            'user' => $recipient,
            'type' => 'new_message',
            'title' => 'New message received',
            'body' => "{$message->sender?->name} sent you a new message.",
            'action_url' => route('conversations.show', $message->conversation_id),
            'data' => ['conversation_id' => $message->conversation_id],
        ];
    }
}
