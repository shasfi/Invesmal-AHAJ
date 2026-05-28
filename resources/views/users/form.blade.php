@extends('layouts.dashboard')

@php $editing = isset($user); @endphp

@section('title', ($editing ? 'Edit' : 'Create') . ' User — Invesmal')

@section('content')

<div class="dashboard-topbar">
    <div class="topbar-left">
        <div>
            <h1 class="topbar-title">{{ $editing ? 'Edit User' : 'Create User' }}</h1>
            <p class="topbar-subtitle">{{ $editing ? 'Update account details' : 'Add a new user to the platform' }}</p>
        </div>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}" enctype="multipart/form-data">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="form-row">
            <div class="auth-form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>
            </div>
            <div class="auth-form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>
            </div>
        </div>

        @if(!$editing)
        <div class="form-row">
            <div class="auth-form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            <div class="auth-form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
            </div>
        </div>
        @endif

        @if(!$editing || auth()->user()?->role === 'admin')
        <div class="form-row">
            <div class="auth-form-group">
                <label for="role">Role <span class="required">*</span></label>
                <select id="role" name="role" class="form-select" {{ !$editing ? 'required' : '' }}>
                    <option value="student_founder" {{ (old('role', $user->role ?? '') == 'student_founder') ? 'selected' : '' }}>Student Founder</option>
                    <option value="investor" {{ (old('role', $user->role ?? '') == 'investor') ? 'selected' : '' }}>Investor</option>
                    <option value="mentor" {{ (old('role', $user->role ?? '') == 'mentor') ? 'selected' : '' }}>Mentor</option>
                    <option value="admin" {{ (old('role', $user->role ?? '') == 'admin') ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="auth-form-group">
                <label for="university">University</label>
                <input type="text" id="university" name="university" class="form-input" value="{{ old('university', $user->university ?? '') }}">
            </div>
        </div>
        @endif

        <div class="auth-form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" class="form-textarea" rows="3">{{ old('bio', $user->bio ?? '') }}</textarea>
        </div>

        <div class="auth-form-group">
            <label for="avatar">Avatar</label>
            <div class="file-upload-wrap">
                <label class="file-label">
                    <i class="fa-solid fa-camera"></i> Choose File
                    <input type="file" id="avatar" name="avatar" class="file-input" accept="image/*">
                </label>
                <span class="file-name">{{ $editing && $user->avatar ? 'Current: ' . basename($user->avatar) : 'No file chosen' }}</span>
            </div>
            @if($editing && $user->avatar)
                <img src="{{ $user->avatar_url }}" alt="Current avatar" class="current-avatar" style="margin-top: 0.5rem;">
            @endif
            <p class="hint">Recommended: 256×256 PNG or JPG</p>
        </div>

        <div class="form-actions">
            <a href="{{ $editing ? route('users.profile', $user) : (auth()->user()?->role === 'admin' ? route('users.index') : route('dashboard')) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">{{ $editing ? 'Update User' : 'Create User' }}</button>
        </div>
    </form>
</div>

@endsection