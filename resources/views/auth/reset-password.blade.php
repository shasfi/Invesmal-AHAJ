@extends('layouts.public')

@section('title', 'Reset Password — Invesmal')

@push('styles')
@include('auth.partials.auth-styles')
@endpush

@section('content')
<div class="auth-page-wrap">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo"></div>
            <h1 class="auth-title">Reset your password</h1>
            <p class="auth-subtitle">Choose a new password, then sign in.</p>
        </div>

        @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
            <input type="hidden" name="role" value="{{ $role ?? old('role') }}">

            <div class="auth-form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="••••••••" autofocus>
            </div>

            <div class="auth-form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            </div>

            <button type="submit" class="auth-submit-btn">Save new password</button>
        </form>

        <p class="auth-alt">
            <a href="{{ route('login') }}">Back to sign in</a>
        </p>
    </div>
</div>
@endsection
