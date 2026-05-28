<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\UserRegistered;
use App\Events\StartupCreated;
use App\Events\StartupVerified;
use App\Events\InvestmentRequested;
use App\Events\InvestmentStatusChanged;
use App\Events\MeetingScheduled;
use App\Events\DocumentUploaded;

class LogUserActivity
{
    public function handle($event): void
    {
        $description = match (get_class($event)) {
            UserRegistered::class => "User {$event->user->name} registered",
            StartupCreated::class => "Startup '{$event->startup->name}' created",
            StartupVerified::class => "Startup '{$event->startup->name}' verified",
            InvestmentRequested::class => "Investment requested for startup #{$event->investment->startup_id}",
            InvestmentStatusChanged::class => "Investment #{$event->investment->id} status changed to {$event->investment->status}",
            MeetingScheduled::class => "Meeting scheduled: {$event->meeting->title}",
            DocumentUploaded::class => "Document '{$event->document->name}' uploaded",
            default => 'Activity recorded',
        };

        $userId = match (get_class($event)) {
            UserRegistered::class => $event->user->id,
            StartupCreated::class => $event->startup->founder_id,
            StartupVerified::class => $event->startup->founder_id,
            InvestmentRequested::class => $event->investment->investor_id,
            MeetingScheduled::class => $event->meeting->scheduler_id,
            DocumentUploaded::class => $event->document->user_id,
            default => auth()->id(),
        };

        ActivityLog::create([
            'user_id' => $userId,
            'action' => class_basename($event),
            'description' => $description,
        ]);
    }
}