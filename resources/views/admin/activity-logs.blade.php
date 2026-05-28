@extends('layouts.dashboard')

@section('title', 'Activity Logs')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/admin/activity-logs.css',
    ]])
@endpush

@section('content')
@php
$logs = $logs ?? [];
$users = $users ?? [];
$actions = $actions ?? [];
@endphp

<div class="activity-logs-hero">
    <h1 style="font-size:1.75rem;font-weight:700;margin-bottom:0.35rem;letter-spacing:-0.02em;">
        <i class="fa-solid fa-clock-rotate-left" style="color:var(--accent-soft);margin-right:0.5rem;"></i>
        Activity Logs
    </h1>
    <p style="color:var(--text-secondary);font-size:0.9rem;">Track and monitor all platform activity</p>
</div>

{{-- Filters --}}
<div class="activity-filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label class="filter-label">User</label>
            <select name="user_id" class="filter-select">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Action</label>
            <select name="action" class="filter-select">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">From Date</label>
            <input type="date" name="from" value="{{ request('from') }}" class="filter-input">
        </div>
        <button type="submit" class="filter-btn">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(request()->anyFilled(['user_id','action','from']))
            <a href="{{ route('admin.activity-logs.index') }}" class="filter-clear">
                <i class="fa-solid fa-xmark"></i> Clear
            </a>
        @endif
    </form>
</div>

{{-- Logs Table --}}
<div class="activity-logs-card">
    <div style="overflow-x:auto;">
        <table class="activity-logs-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="color:var(--text-secondary);white-space:nowrap;">
                        <div style="font-weight:500;color:var(--text);">{{ $log->created_at?->format('M d, Y') }}</div>
                        <div style="font-size:0.8125rem;">{{ $log->created_at?->format('H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text);">{{ $log->user?->name ?? 'System' }}</div>
                        @if($log->user)
                        <div style="font-size:0.8125rem;color:var(--text-secondary);">{{ $log->user->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="activity-action-badge {{ strtolower($log->action) }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="color:var(--text-secondary);">{{ $log->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="fa-solid fa-clipboard-list" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.5;"></i>
                        <p style="font-size:0.9375rem;">No activity logs found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="activity-logs-footer">
        {{ $logs->links() }}
    </div>
</div>
@endsection