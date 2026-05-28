<?php

namespace App\Http\Controllers;

use App\Events\DocumentUploaded;
use App\Models\Document;
use App\Models\Startup;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDocumentRequest;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $documents = Document::with(['startup', 'user'])->latest()->paginate(12);
            $startups = Startup::orderBy('name')->limit(50)->get();

            return view('documents.index', compact('documents', 'startups'));
        }

        $documents = Document::query()
            ->with(['startup', 'user'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('startup', fn ($s) => $s->where('founder_id', $user->id));
            })
            ->when($user->role === 'investor', function ($q) {
                $q->orWhereHas('startup', fn ($s) => $s->where('is_verified', true));
            })
            ->latest()
            ->paginate(12);

        $startups = $user->role === 'student_founder'
            ? $user->startups()->orderBy('name')->get()
            : Startup::where('is_verified', true)->orderBy('name')->limit(50)->get();

        return view('documents.index', compact('documents', 'startups'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $document = $this->documentService->upload(
            $request->user(),
            $request->file('file'),
            $request->validated()
        );

        event(new DocumentUploaded($document));

        return redirect()
            ->route('documents.index')
            ->with('success', "Document uploaded (version {$document->version}).");
    }

    public function download(Document $document)
    {
        $this->authorize('download', $document);

        return $this->documentService->download($document);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $this->documentService->delete($document);

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document deleted.');
    }
}
