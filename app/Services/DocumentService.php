<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function upload(User $user, UploadedFile $file, array $data): Document
    {
        $startupId = $data['startup_id'] ?? null;
        $type = $this->normalizeType($data['type'] ?? 'other');
        $displayName = $data['name'] ?? $file->getClientOriginalName();

        $versionQuery = Document::query()
            ->where('user_id', $user->id)
            ->where('type', $type);

        if ($startupId) {
            $versionQuery->where('startup_id', $startupId);
        } else {
            $versionQuery->whereNull('startup_id');
        }

        $version = ((int) $versionQuery->max('version')) + 1;

        $folder = $startupId
            ? "documents/startups/{$startupId}"
            : "documents/users/{$user->id}";

        $path = $file->store($folder, 'public');

        return Document::create([
            'user_id' => $user->id,
            'startup_id' => $startupId,
            'type' => $type,
            'filename' => basename($path),
            'original_name' => $displayName,
            'path' => $path,
            'version' => $version,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $data['description'] ?? null,
        ]);
    }

    public function getUserDocuments(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Document::where('user_id', $user->id)
            ->orWhereHas('startup', fn ($q) => $q->where('founder_id', $user->id))
            ->with(['startup', 'user'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStartupDocuments(Startup $startup): \Illuminate\Database\Eloquent\Collection
    {
        return Document::where('startup_id', $startup->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    public function download(Document $document): StreamedResponse
    {
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $document->path,
            $document->original_name
        );
    }

    public function delete(Document $document): void
    {
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        $document->delete();
    }

    protected function normalizeType(string $type): string
    {
        return match ($type) {
            'financials' => 'other',
            'financial' => 'other',
            'legal' => 'other',
            default => in_array($type, ['pitch_deck', 'business_plan', 'other'], true) ? $type : 'other',
        };
    }
}
