@extends('layouts.public')

@section('title', 'Choose Role — Invesmal')

@section('content')
<div class="auth-page-wrap" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6rem 2rem;">
    <div class="auth-card" style="max-width:460px;width:100%;padding:2.5rem;background:var(--surface-strong);border:1px solid var(--border);border-radius:var(--radius-xl);">
        <h1 style="font-size:1.5rem;font-weight:800;margin-bottom:0.5rem;">Choose your role</h1>
        <p style="color:var(--text-secondary);margin-bottom:1.5rem;">Complete Firebase sign-up for Invesmal.</p>
        <form method="POST" action="{{ route('firebase.complete') }}">
            @csrf
            <label style="display:block;font-weight:600;margin-bottom:0.5rem;">I am a</label>
            <select name="role" required style="width:100%;padding:0.75rem;margin-bottom:1rem;border-radius:var(--radius-md);border:1px solid var(--border);background:var(--surface);color:var(--text);">
                <option value="">Select role</option>
                <option value="student_founder">Student Founder</option>
                <option value="investor">Investor</option>
                <option value="mentor">Mentor</option>
            </select>
            <button type="submit" class="auth-submit-btn" style="width:100%;padding:0.85rem;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-md);font-weight:700;cursor:pointer;">Continue</button>
        </form>
    </div>
</div>
@endsection
