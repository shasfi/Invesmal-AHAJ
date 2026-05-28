@extends('layouts.public')

@section('title', 'Verify Email — Invesmal')

@section('content')
<div class="auth-page-wrap invesmal-auth-wrap" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6rem 2rem;">
    <div class="auth-card modern-card modern-card--lift" style="max-width:400px;width:100%;padding:2.5rem;text-align:center;">
        <h1 style="font-size:1.5rem;font-weight:800;margin-bottom:1rem;">Verify email</h1>
        <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:1rem;">
            {{ $email }} · {{ str_replace('_', ' ', $role) }}
        </p>

        @if(session('status') === 'not-verified-yet')
            <p style="font-size:0.8rem;color:var(--warning, #c9a227);margin-bottom:1rem;">Not verified yet. Open the link in your email first.</p>
        @endif

        @if($mailError)
            <p style="font-size:0.8rem;color:var(--danger);margin-bottom:1rem;">{{ $mailError }}</p>
        @elseif(!$mailSent)
            <p style="font-size:0.8rem;color:var(--danger);margin-bottom:1rem;">Email could not be sent.</p>
        @endif

        <form method="POST" action="{{ route('verification.check-status') }}" style="margin-bottom:0.75rem;">
            @csrf
            <button type="submit" class="auth-submit-btn" style="width:100%;">I clicked the email link</button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom:0.75rem;">
            @csrf
            <button type="submit" style="width:100%;padding:0.65rem;background:transparent;border:1px solid var(--border);border-radius:var(--radius-md);color:var(--accent-soft);font-size:0.85rem;cursor:pointer;">Resend email</button>
        </form>

        <form method="POST" action="{{ route('verification.wrong-account') }}">
            @csrf
            <button type="submit" style="width:100%;padding:0.65rem;background:transparent;border:none;color:var(--muted);font-size:0.85rem;cursor:pointer;text-decoration:underline;">
                Wrong account? Use another email
            </button>
        </form>
    </div>
</div>
@endsection
