@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/documents/documents.css',
    ]])
@endpush

@section('title', 'Documents — Invesmal')

@section('content')

<div class="dashboard-topbar">
    <div class="topbar-left">
        <div>
            <h1 class="topbar-title">Documents</h1>
            <p class="topbar-subtitle">Upload & manage your files</p>
        </div>
    </div>
    <div class="topbar-right">
        <a href="#upload-section" class="btn-primary"><i class="fa-solid fa-plus"></i> Upload</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

{{-- Upload Section --}}
<div class="form-card" id="upload-section" style="margin-bottom: 2rem;">
    <h2 class="section-title"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Document</h2>
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="auth-form-group">
            <label for="name">Document Name</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="e.g. Q1 Business Plan" maxlength="255">
        </div>
        <div class="form-row">
        <div class="auth-form-group">
                <label for="type">Document Type <span class="required">*</span></label>
                <select id="type" name="type" class="form-select" required>
                    <option value="">Select type</option>
                    <option value="pitch_deck" {{ old('type') == 'pitch_deck' ? 'selected' : '' }}>Pitch Deck</option>
                    <option value="business_plan" {{ old('type') == 'business_plan' ? 'selected' : '' }}>Business Plan</option>
                    <option value="financials" {{ old('type') == 'financials' ? 'selected' : '' }}>Financials</option>
                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="auth-form-group">
                <label for="startup_id">Related Startup</label>
                <select id="startup_id" name="startup_id" class="form-select">
                    <option value="">None</option>
                    @foreach($startups ?? [] as $s)
                        <option value="{{ $s->id }}" {{ old('startup_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="upload-zone" onclick="document.getElementById('file').click()">
            <i class="fa-solid fa-file-arrow-up"></i>
            <p class="upload-zone-text">Click to upload or drag and drop</p>
            <p class="upload-zone-hint">PDF, DOCX, PPTX, XLSX (max 10MB)</p>
        </div>
        <input type="file" id="file" name="file" class="file-input" required>
        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</button>
        </div>
    </form>
</div>

{{-- Documents List --}}
@if($documents->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-file empty-icon"></i>
        <h3>No documents yet</h3>
        <p>Upload your first document above.</p>
    </div>
@else
    <div class="document-grid">
        @foreach($documents as $doc)
        <div class="document-card">
            <i class="fa-solid fa-file-lines document-icon"></i>
            <a href="{{ Storage::url($doc->path) }}" target="_blank" class="document-name">{{ $doc->original_name }}</a>
            <span class="document-type">{{ strtoupper($doc->type) }} · v{{ $doc->version }}</span>
            <div class="document-meta">
                <span class="document-size">{{ number_format($doc->size / 1024, 1) }} KB</span>
                <span class="document-date">{{ $doc->created_at->format('M j, Y') }}</span>
            </div>
            @if($doc->startup)
            <div class="document-meta" style="margin-top: 0.5rem;">
                <span style="color: var(--accent-soft); font-size: 0.8rem;"><i class="fa-solid fa-rocket"></i> {{ $doc->startup->name }}</span>
            </div>
            @endif
            <div class="document-actions">
                <a href="{{ route('documents.download', $doc) }}" class="action-link"><i class="fa-solid fa-download"></i> Download</a>
                <form method="POST" action="{{ route('documents.destroy', $doc) }}" style="display: inline;">
                    @csrf @method('DELETE')
                    <button class="btn-ghost" style="color: var(--danger);"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @if(method_exists($documents, 'hasPages') && $documents->hasPages())
        <div class="pagination-wrap">{{ $documents->links() }}</div>
    @endif
@endif

@endsection