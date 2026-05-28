@extends('layouts.dashboard')

@section('title', 'Verification Queue')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/admin/verification.css',
    ]])
@endpush

@section('content')
@php
$pendingUsers = $pendingUsers ?? [];
$pendingStartups = $pendingStartups ?? [];
@endphp

<div class="page-hero">
    <div>
        <h1 class="page-hero__title">
            <i class="fa-solid fa-circle-check" style="color:var(--accent-soft);margin-right:0.5rem;"></i>
            Verification Queue
        </h1>
        <p class="page-hero__subtitle">Review and approve pending users and startups</p>
    </div>
    <div class="page-hero__actions">
        <span class="verification-count">{{ count($pendingUsers) + count($pendingStartups) }} Pending</span>
    </div>
</div>

{{-- Pending Users --}}
<div class="verification-section">
    <div class="verification-header">
        <h2 class="verification-title">
            <i class="fa-solid fa-users" style="color:var(--primary);"></i>
            Pending Users
        </h2>
        <span class="verification-count">{{ count($pendingUsers) }}</span>
    </div>
    @if(count($pendingUsers))
        <table class="verification-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingUsers as $user)
                <tr>
                    <td>
                        <div class="verification-user-cell">
                            <div class="verification-avatar">{{ substr($user->name, 0, 1) }}</div>
                            <div>
                                <div class="verification-name">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="verification-email">{{ $user->email }}</td>
                    <td>
                        <span class="verification-badge verification-badge-role">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </td>
                    <td class="verification-email">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="verification-actions">
                            <form method="POST" action="{{ route('admin.verify.user', $user) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="verification-btn verification-btn-approve">
                                    <i class="fa-solid fa-check"></i> Approve
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="verification-empty">
            <i class="fa-solid fa-user-check verification-empty-icon"></i>
            <h3>No pending users</h3>
            <p>All users have been verified</p>
        </div>
    @endif
</div>

{{-- Pending Startups --}}
<div class="verification-section">
    <div class="verification-header">
        <h2 class="verification-title">
            <i class="fa-solid fa-rocket" style="color:var(--primary);"></i>
            Pending Startups
        </h2>
        <span class="verification-count">{{ count($pendingStartups) }}</span>
    </div>
    @if(count($pendingStartups))
        <table class="verification-table">
            <thead>
                <tr>
                    <th>Startup</th>
                    <th>Industry</th>
                    <th>Stage</th>
                    <th>Founder</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingStartups as $startup)
                <tr>
                    <td>
                        <div class="verification-name">{{ $startup->name }}</div>
                    </td>
                    <td class="verification-email">{{ $startup->industry }}</td>
                    <td>
                        <span class="verification-badge verification-badge-stage">
                            {{ ucfirst($startup->stage) }}
                        </span>
                    </td>
                    <td class="verification-email">{{ $startup->founder?->name ?? 'N/A' }}</td>
                    <td>
                        <div class="verification-actions">
                            <form method="POST" action="{{ route('admin.verify.startup', $startup) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="verification-btn verification-btn-approve">
                                    <i class="fa-solid fa-check"></i> Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.flag', $startup) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="flag_reason" value="Rejected during verification">
                                <button type="submit" class="verification-btn verification-btn-reject">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="verification-empty">
            <i class="fa-solid fa-rocket verification-empty-icon"></i>
            <h3>No pending startups</h3>
            <p>All startups have been verified</p>
        </div>
    @endif
</div>
@endsection