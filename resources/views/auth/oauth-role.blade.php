@extends('layouts.public')

@section('title', 'Choose Role â€” Invesmal')

@push('styles')
@include('auth.partials.auth-styles')
@endpush

@section('content')
<div class="auth-page-wrap">
<div class="auth-card">
    <h1 class="auth-title">Almost done</h1>
    <p style="color:var(--text-secondary);margin-bottom:1.5rem;font-size:0.95rem;">Select your role on Invesmal to finish signing in.</p>

    @if($errors->any())
        <div style="color:var(--danger);margin-bottom:1rem;font-size:0.875rem;">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('oauth.complete') }}">
        @csrf
        <div class="auth-form-group">
            <label for="role">I am a</label>
            <select id="role" name="role" required>
                <option value="">Select your role</option>
                <option value="student_founder">Student Founder</option>
                <option value="investor">Investor</option>
                <option value="mentor">Mentor</option>
            </select>
        </div>
        <button type="submit" class="auth-submit-btn">Continue to Dashboard</button>
    </form>
</div>
</div>
@endsection
