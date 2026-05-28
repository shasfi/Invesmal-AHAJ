@extends('layouts.public')

@section('title', ($mode === 'login' ? 'Sign In' : 'Create Account') . ' — Invesmal')

@push('styles')
@include('auth.partials.auth-styles')
@endpush

@section('content')
<div class="auth-page-wrap invesmal-auth-wrap">
    <div class="auth-card modern-card modern-card--lift">
        <div class="auth-header">
            <div class="auth-logo"></div>
            <h1 class="auth-title">{{ $mode === 'login' ? 'Welcome back' : 'Get started' }}</h1>
            <p class="auth-subtitle">{{ $mode === 'login' ? 'Sign in to your account' : 'Create your Invesmal account' }}</p>
        </div>

        @if(session('status') && $mode === 'login')
            <div class="auth-status" style="margin-bottom:1.25rem;text-align:left;">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @include('auth.partials.firebase-auth')

        <p style="text-align:center;font-size:0.75rem;color:var(--muted);margin:0 0 1rem;">— or use email —</p>

        <form method="POST" action="{{ $mode === 'login' ? route('login.attempt') : route('register.attempt') }}">
            @csrf

            @if($mode === 'register')
                <div class="auth-form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="John Doe">
                </div>
            @endif

            <div class="auth-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@university.edu">
            </div>

            <div class="auth-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="{{ $mode === 'login' ? 'current-password' : 'new-password' }}" placeholder="••••••••">
            </div>

            @if($mode === 'login')
                @include('auth.partials.role-select', ['showAdmin' => true, 'label' => 'I am a'])
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;font-size:0.85rem;">
                    <label style="display:flex;align-items:center;gap:0.5rem;color:var(--text-secondary);cursor:pointer;font-weight:500;">
                        <input type="checkbox" name="remember" style="accent-color:var(--accent-soft);">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" style="color:var(--accent-soft);font-weight:600;text-decoration:none;">Forgot password?</a>
                </div>
            @endif

            @if($mode === 'register')
                <div class="auth-form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                </div>
                @include('auth.partials.role-select', ['label' => 'I am a'])
                @include('auth.partials.recaptcha', ['mode' => 'register'])
            @endif

            <button type="submit" class="auth-submit-btn">
                {{ $mode === 'login' ? 'Sign In' : 'Create Account' }}
            </button>

            <p class="auth-alt">
                @if($mode === 'login')
                    Don't have an account? <a href="{{ route('register') }}">Sign up</a>
                @else
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                @endif
            </p>
        </form>
    </div>
</div>
@endsection
