<?php

namespace App\Events;

use App\Models\Startup;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartupCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Startup $startup) {}
}