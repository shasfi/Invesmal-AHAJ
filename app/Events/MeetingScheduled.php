<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingScheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Meeting $meeting) {}
}