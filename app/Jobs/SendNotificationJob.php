<?php

namespace App\Jobs;

use App\Mail\NotificationMail;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $type,
        public string $title,
        public ?string $body = null,
        public ?string $actionUrl = null,
        public ?array $data = null,
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        if (!$this->user->email) {
            return;
        }

        $notification = $notificationService->notify(
            $this->user,
            $this->type,
            $this->title,
            $this->body,
            $this->actionUrl,
            $this->data
        );

        if (!$notification) {
            return;
        }

        Mail::to($this->user->email)->send(new NotificationMail($this->user, $notification));
    }
}
