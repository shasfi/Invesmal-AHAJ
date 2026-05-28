@extends('layouts.public')

@section('title', 'Forgot Password — Invesmal')

@push('styles')
@include('auth.partials.auth-styles')
@endpush

@section('content')
<div class="auth-page-wrap">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo"></div>
            <h1 class="auth-title">Forgot password?</h1>
            <p class="auth-subtitle">Check your inbox and spam folder for the reset link.</p>
        </div>

        @if(session('reset_sent') && !$errors->any())
            <p class="auth-status">
                If an account exists for that email, we sent a password reset link. Check your inbox and Spam folder.
            </p>

            @if(session('dev_reset_url'))
                <p class="auth-status" style="margin-top:1rem;word-break:break-all;">
                    <a href="{{ session('dev_reset_url') }}" style="color:var(--accent-soft);font-weight:600;">Dev: open reset page</a>
                </p>
            @endif

            <p class="auth-alt" style="margin-top:2rem;">
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>
        @else
            @if(session('dev_reset_url'))
                <p class="auth-status" style="margin-bottom:1rem;word-break:break-all;">
                    <a href="{{ session('dev_reset_url') }}" style="color:var(--accent-soft);font-weight:600;">Open password reset page</a>
                </p>
            @endif

            @if($errors->any())
                <div class="auth-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="auth-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@university.edu" autofocus>
                </div>
                @include('auth.partials.role-select', ['showAdmin' => true])
                <button type="submit" class="auth-submit-btn">Send reset link</button>
            </form>

            <p class="auth-alt">
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>
        @endif
    </div>
</div>
@endsection
