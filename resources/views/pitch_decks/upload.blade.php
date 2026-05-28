@extends('layouts.dashboard')

@section('title', 'Upload Pitch Deck for Analysis â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-upload">
    <div class="pd-upload__header">
        <a href="{{ route('pitch_decks.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> Back to decks</a>
        <h1>Upload & Analyze Pitch Deck</h1>
        <p>Upload your existing pitch deck (PDF or PPTX) and get AI-powered analysis with actionable improvements.</p>
    </div>

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('pitch_decks.upload') }}" method="POST" enctype="multipart/form-data" class="pd-upload__form">
        @csrf

        <div class="pd-form-group">
            <label for="title" class="pd-label">Deck Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-input" 
                   value="{{ old('title') }}" 
                   placeholder="My Startup Pitch Deck" required maxlength="255">
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="pd-form-group">
            <label class="pd-label">Upload File <span class="required">*</span>
                <span class="label-hint">PDF or PPTX, max 10MB</span>
            </label>
            <div class="pd-dropzone" id="dropZone">
                <input type="file" id="fileInput" name="file" accept=".pdf,.pptx" required>
                <div class="pd-dropzone__inner">
                    <i class="fa-solid fa-cloud-arrow-up pd-dropzone__icon"></i>
                    <p class="pd-dropzone__text">Drag & drop your pitch deck here</p>
                    <p class="pd-dropzone__sub">or click to browse</p>
                    <span id="fileName" class="pd-dropzone__file"></span>
                </div>
            </div>
            @error('file') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="pd-form-group">
            <label for="startup_description" class="pd-label">
                Startup Description
                <span class="label-hint">Brief context to help the AI understand your deck better (optional)</span>
            </label>
            <textarea id="startup_description" name="startup_description" class="form-textarea"
                      rows="5" placeholder="Optional: Brief description of your startup..." 
                      maxlength="5000">{{ old('startup_description') }}</textarea>
            @error('startup_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="pd-form-actions">
            <a href="{{ route('pitch_decks.index') }}" class="pp-btn pp-btn--ghost">Cancel</a>
            <button type="submit" class="pp-btn pp-btn--primary pp-btn--large">
                <i class="fa-solid fa-microscope"></i> Upload & Analyze
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('pd-dropzone--active');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('pd-dropzone--active');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('pd-dropzone--active');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileName();
        }
    });

    fileInput.addEventListener('change', updateFileName);

    function updateFileName() {
        if (fileInput.files.length) {
            const f = fileInput.files[0];
            fileName.textContent = f.name + ' (' + (f.size / 1024).toFixed(1) + ' KB)';
        }
    }
</script>
@endpush
