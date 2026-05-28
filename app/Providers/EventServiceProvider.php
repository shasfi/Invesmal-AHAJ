<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\StartupCreated;
use App\Events\StartupVerified;
use App\Events\InvestmentRequested;
use App\Events\InvestmentStatusChanged;
use App\Events\MeetingScheduled;
use App\Events\MessageSent;
use App\Events\DocumentUploaded;
use App\Listeners\LogUserActivity;
use App\Listeners\SendAppNotification;
use App\Listeners\SendEmailNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            LogUserActivity::class,
            SendAppNotification::class,
        ],
        StartupCreated::class => [
            LogUserActivity::class,
            SendAppNotification::class,
        ],
        StartupVerified::class => [
            LogUserActivity::class,
            SendAppNotification::class,
            SendEmailNotification::class,
        ],
        InvestmentRequested::class => [
            LogUserActivity::class,
            SendAppNotification::class,
            SendEmailNotification::class,
        ],
        InvestmentStatusChanged::class => [
            LogUserActivity::class,
            SendAppNotification::class,
            SendEmailNotification::class,
        ],
        MeetingScheduled::class => [
            LogUserActivity::class,
            SendAppNotification::class,
            SendEmailNotification::class,
        ],
        MessageSent::class => [
            SendAppNotification::class,
            SendEmailNotification::class,
        ],
        DocumentUploaded::class => [
            LogUserActivity::class,
            SendAppNotification::class,
        ],
    ];
}