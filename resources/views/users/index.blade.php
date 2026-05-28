@extends('layouts.dashboard')

@section('title', 'User Management â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/users/users-list.css',
    ]])
@endpush

@section('content')

    <div class="user-management-hero">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="font-size:1.75rem;font-weight:700;margin-bottom:0.35rem;letter-spacing:-0.02em;">
                    <i class="fa-solid fa-users-gear" style="color:var(--accent-soft);margin-right:0.5rem;"></i>
                    User Management
                </h1>
                <p style="color:var(--text-secondary);font-size:0.9rem;">Manage platform users, roles, and verification status</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Add User
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success" style="margin-bottom:1.5rem;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="user-table-card">
        <div class="user-table-header">
            <div>
                <div class="user-table-title">
                    <i class="fa-solid fa-list" style="color:var(--primary);"></i>
                    All Users
                </div>
                <div class="user-table-count">{{ $users->total() }} users registered</div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>University</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="premium-user-cell">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="premium-avatar">
                                <div class="premium-user-info">
                                    <div class="premium-user-name">{{ $user->name }}</div>
                                    <div class="premium-user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="premium-role-badge {{ $user->role }}">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td style="color:var(--text-secondary);">{{ $user->university ?? 'â€”' }}</td>
                        <td>
                            @if($user->is_verified)
                                <span class="premium-verified-badge verified">
                                    <i class="fa-solid fa-circle-check"></i> Verified
                                </span>
                            @else
                                <span class="premium-verified-badge unverified">
                                    <i class="fa-solid fa-clock"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td style="color:var(--text-secondary);">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="premium-actions">
                                <a href="{{ route('users.profile', $user) }}" class="premium-action-btn" title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="premium-action-btn" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted);">
                            <i class="fa-solid fa-users-slash" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.5;"></i>
                            <p style="font-size:0.9375rem;">No users found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="premium-table-footer">
            {{ $users->links() }}
        </div>
    </div>

@endsection