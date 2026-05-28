<?php

namespace App\Jobs;

use App\Models\PitchDeck;
use App\Services\PitchDeckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzePitchDeckJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public PitchDeck $pitchDeck) {}

    public function handle(PitchDeckService $pitchDeckService): void
    {
        $pitchDeckService->analyze($this->pitchDeck);
    }
}
