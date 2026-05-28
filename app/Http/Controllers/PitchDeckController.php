<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzePitchDeckJob;
use App\Models\PitchDeck;
use App\Services\PitchDeckService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PitchDeckController extends Controller
{
    public function __construct(
        protected PitchDeckService $pitchDeckService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PitchDeck::class);

        $decks = PitchDeck::where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('pitch_decks.index', compact('decks'));
    }

    public function create()
    {
        $this->authorize('generate', PitchDeck::class);

        return view('pitch_decks.create');
    }

    public function show(PitchDeck $pitchDeck)
    {
        $this->authorize('view', $pitchDeck);

        $deck = $pitchDeck;

        return view('pitch_decks.show', compact('deck'));
    }

    public function generate(Request $request)
    {
        $this->authorize('generate', PitchDeck::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'startup_description' => ['required', 'string', 'min:30', 'max:5000'],
        ]);

        $content = $this->pitchDeckService->generate(
            $validated['startup_description'],
            $validated['title']
        );

        if (!$content) {
            return back()
                ->withInput()
                ->with('error', 'Pitch deck generation failed. Add OPENAI_API_KEY in .env for full AI, or contact admin.');
        }

        $isDemo = !app(\App\Services\AIService::class)->isConfigured();

        $deck = PitchDeck::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'startup_description' => $validated['startup_description'],
            'content_json' => $content,
            'status' => 'generated',
        ]);

        $message = $isDemo
            ? 'Demo pitch deck created (no OpenAI key). Add OPENAI_API_KEY in .env for real AI. Review and edit before analyzing.'
            : 'Pitch deck generated. Review and edit before analyzing.';

        return redirect()
            ->route('pitch_decks.edit', $deck)
            ->with('success', $message);
    }

    public function upload(Request $request)
    {
        $this->authorize('create', PitchDeck::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,ppt,pptx'],
            'title' => ['required', 'string', 'max:255'],
            'startup_description' => ['nullable', 'string', 'max:5000'],
        ]);

        $file = $request->file('file');
        $path = $file->store('pitch_decks/' . auth()->id(), 'public');
        $extension = strtolower($file->getClientOriginalExtension());

        $deck = PitchDeck::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'startup_description' => $validated['startup_description'] ?? null,
            'file_path' => $path,
            'file_type' => in_array($extension, ['ppt', 'pptx']) ? 'pptx' : 'pdf',
            'status' => 'draft',
        ]);

        return redirect()
            ->route('pitch_decks.analyze', $deck)
            ->with('status', 'Deck uploaded. Starting AI analysis…');
    }

    public function analyze(PitchDeck $pitchDeck)
    {
        $this->authorize('analyze', $pitchDeck);

        if (config('queue.default') === 'sync') {
            $result = $this->pitchDeckService->analyze($pitchDeck);

            if (!$result) {
                return redirect()
                    ->route('pitch_decks.edit', $pitchDeck)
                    ->with('error', 'Analysis failed. Ensure OPENAI_API_KEY is configured and the deck has content to analyze.');
            }

            return redirect()
                ->route('pitch_decks.analysis', $pitchDeck)
                ->with('success', 'Analysis complete.');
        }

        AnalyzePitchDeckJob::dispatch($pitchDeck);

        return redirect()
            ->route('pitch_decks.analysis', $pitchDeck)
            ->with('status', 'AI analysis is being processed. Refresh this page in a moment for results.');
    }

    public function analysis(PitchDeck $pitchDeck)
    {
        $this->authorize('view', $pitchDeck);

        $deck = $pitchDeck;
        $analysis = $pitchDeck->ai_analysis ?? [];

        return view('pitch_decks.analysis', compact('deck', 'analysis'));
    }

    public function edit(PitchDeck $pitchDeck)
    {
        $this->authorize('update', $pitchDeck);

        $deck = $pitchDeck;

        return view('pitch_decks.edit', compact('deck'));
    }

    public function update(Request $request, PitchDeck $pitchDeck)
    {
        $this->authorize('update', $pitchDeck);

        $validated = $request->validate([
            'tagline' => ['required', 'string', 'max:500'],
            'executive_summary' => ['required', 'string', 'max:2000'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'string', 'max:100'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.content' => ['required', 'string', 'max:5000'],
        ]);

        $this->pitchDeckService->saveEdits(
            $pitchDeck,
            $validated['sections'],
            $validated['executive_summary'],
            $validated['tagline']
        );

        return redirect()
            ->route('pitch_decks.edit', $pitchDeck)
            ->with('success', 'Pitch deck saved.');
    }

    public function destroy(PitchDeck $pitchDeck)
    {
        $this->authorize('delete', $pitchDeck);

        if ($pitchDeck->file_path && Storage::disk('public')->exists($pitchDeck->file_path)) {
            Storage::disk('public')->delete($pitchDeck->file_path);
        }

        $pitchDeck->delete();

        return redirect()
            ->route('pitch_decks.index')
            ->with('success', 'Pitch deck deleted.');
    }

    public function publicSummary(PitchDeck $pitchDeck)
    {
        if (!in_array($pitchDeck->status, ['analyzed', 'final'], true)) {
            abort(403, 'Analysis not yet available.');
        }

        return response()->json([
            'name' => $pitchDeck->title,
            'status' => $pitchDeck->status,
            'analysis' => $pitchDeck->ai_analysis,
            'score' => $pitchDeck->ai_score,
        ]);
    }
}
