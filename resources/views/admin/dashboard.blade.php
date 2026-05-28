@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/admin/admin-dashboard.css']])
@endpush

@section('content')
@php
$stats = $stats ?? [];
$recentActivities = $recentActivities ?? [];
$pendingCount = $pendingCount ?? 0;
$unverifiedUsers = $unverifiedUsers ?? [];
$unverifiedStartups = $unverifiedStartups ?? [];
$userGrowthData = $userGrowthData ?? [];
$startupStageData = $startupStageData ?? [];
$roleDistributionData = $roleDistributionData ?? [];
$investmentTrendData = $investmentTrendData ?? [];
$recentInteractions = $recentInteractions ?? [];
$totalInvestmentAmount = $totalInvestmentAmount ?? 0;
$activeInvestors = $activeInvestors ?? 0;
$activeFounders = $activeFounders ?? 0;
@endphp

<div class="page-hero">
    <div>
        <h1 class="page-hero__title">Admin Dashboard</h1>
        <p class="page-hero__subtitle">Platform overview and management controls</p>
    </div>
    <div class="page-hero__actions">
        <a href="{{ route('admin.verification.index') }}" class="btn-primary">
            <i class="fa-solid fa-circle-check"></i> Verification Queue
            @if($pendingCount > 0)
                <span style="background:var(--danger);color:white;font-size:0.7rem;padding:0.15rem 0.4rem;border-radius:99px;margin-left:0.25rem;">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>
</div>

{{-- Stats Row --}}
<div class="admin-stats-grid stagger-children">
    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="admin-stat-value">{{ $stats['total_users'] ?? 0 }}</div>
        <div class="admin-stat-label">Total Users</div>
        <div class="admin-stat-trend positive">
            <i class="fa-solid fa-arrow-trend-up"></i>
            <span>Active platform</span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-rocket"></i>
        </div>
        <div class="admin-stat-value">{{ $stats['total_startups'] ?? 0 }}</div>
        <div class="admin-stat-label">Total Startups</div>
        <div class="admin-stat-trend positive">
            <i class="fa-solid fa-arrow-trend-up"></i>
            <span>Growing ecosystem</span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div class="admin-stat-value">{{ $stats['total_investments'] ?? 0 }}</div>
        <div class="admin-stat-label">Investments</div>
        <div class="admin-stat-trend neutral">
            <i class="fa-solid fa-chart-line"></i>
            <span>Tracking progress</span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="background: linear-gradient(135deg, var(--warning), var(--danger));">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="admin-stat-value" style="color: var(--warning);">{{ $pendingCount }}</div>
        <div class="admin-stat-label">Pending Verifications</div>
        <div class="admin-stat-trend" style="color: var(--warning);">
            <i class="fa-solid fa-exclamation-circle"></i>
            <span>Requires attention</span>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="admin-stats-grid stagger-children" style="margin-bottom: 2rem;">
    <div class="admin-stat-card" style="grid-column: span 2;">
        <div class="admin-stat-header">
            <i class="fa-solid fa-chart-line"></i>
            <span>User Growth (30 Days)</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-header">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Startup Stages</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="startupStageChart"></canvas>
        </div>
    </div>
</div>

<div class="admin-stats-grid stagger-children" style="margin-bottom: 2rem;">
    <div class="admin-stat-card">
        <div class="admin-stat-header">
            <i class="fa-solid fa-users"></i>
            <span>Role Distribution</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="roleDistributionChart"></canvas>
        </div>
    </div>
    <div class="admin-stat-card" style="grid-column: span 2;">
        <div class="admin-stat-header">
            <i class="fa-solid fa-chart-bar"></i>
            <span>Investment Trends (30 Days)</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="investmentTrendChart"></canvas>
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div class="admin-quick-actions">
    <a href="{{ route('admin.verification.index') }}" class="admin-action-btn admin-action-btn-primary">
        <i class="fa-solid fa-circle-check"></i> Verification Queue
    </a>
    <a href="{{ route('admin.activity-logs.index') }}" class="admin-action-btn admin-action-btn-secondary">
        <i class="fa-solid fa-clock-rotate-left"></i> Activity Logs
    </a>
    <a href="{{ route('startups.index') }}" class="admin-action-btn admin-action-btn-secondary">
        <i class="fa-solid fa-rocket"></i> Manage Startups
    </a>
    <a href="{{ route('users.index') }}" class="admin-action-btn admin-action-btn-secondary">
        <i class="fa-solid fa-users-gear"></i> Manage Users
    </a>
</div>

{{-- Unverified Accounts Section --}}
@if(count($unverifiedUsers) > 0 || count($unverifiedStartups) > 0)
<div class="unverified-section">
    <h3 class="unverified-section-title">
        <i class="fa-solid fa-triangle-exclamation" style="color:var(--warning);"></i>
        Unverified Accounts
        <span style="margin-left:auto;font-size:0.875rem;color:var(--text-secondary);">
            {{ count($unverifiedUsers) + count($unverifiedStartups) }} pending
        </span>
    </h3>
    <div class="unverified-list">
        @foreach($unverifiedUsers as $user)
            <div class="unverified-item">
                <div class="unverified-avatar">{{ substr($user->name, 0, 1) }}</div>
                <div class="unverified-info">
                    <div class="unverified-name">{{ $user->name }}</div>
                    <div class="unverified-email">{{ $user->email }}</div>
                </div>
                <span class="unverified-type user">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                <div class="unverified-actions">
                    <form method="POST" action="{{ route('admin.users.update-status', $user) }}" style="display:inline;">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="unverified-btn" style="cursor:pointer;">
                            <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $user->status === 'approved' ? 'selected' : '' }}>Approve</option>
                            <option value="rejected" {{ $user->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </form>
                    <a href="{{ route('users.profile', $user) }}" class="unverified-btn unverified-btn-view">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                </div>
            </div>
        @endforeach
        @foreach($unverifiedStartups as $startup)
            <div class="unverified-item">
                <div class="unverified-avatar"><i class="fa-solid fa-rocket"></i></div>
                <div class="unverified-info">
                    <div class="unverified-name">{{ $startup->name }}</div>
                    <div class="unverified-email">{{ $startup->industry }} • {{ ucfirst($startup->stage) }}</div>
                </div>
                <span class="unverified-type startup">Startup</span>
                <div class="unverified-actions">
                    <form method="POST" action="{{ route('admin.startups.update-status', $startup) }}" style="display:inline;">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="unverified-btn" style="cursor:pointer;">
                            <option value="pending" {{ $startup->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $startup->status === 'approved' ? 'selected' : '' }}>Approve</option>
                            <option value="rejected" {{ $startup->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </form>
                    <a href="{{ route('startups.show', $startup) }}" class="unverified-btn unverified-btn-view">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Activity --}}
<div class="admin-activity-card">
    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fa-solid fa-bolt" style="color:var(--accent-soft);"></i> Recent Activity
    </h3>
    @if(count($recentActivities))
        <div style="display:flex;flex-direction:column;">
            @foreach($recentActivities as $activity)
                <div class="admin-activity-item">
                    <div class="admin-activity-avatar">
                        {{ substr($activity->user?->name ?? 'S', 0, 1) }}
                    </div>
                    <div class="admin-activity-time">{{ $activity->created_at?->diffForHumans() }}</div>
                    <div style="flex:1;">
                        <span class="admin-activity-user">{{ $activity->user?->name ?? 'System' }}</span>
                        <span class="admin-activity-action">{{ $activity->action }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.9rem;">
            <i class="fa-solid fa-inbox" style="font-size:2rem;margin-bottom:0.75rem;opacity:0.5;"></i>
            <p>No recent activity.</p>
        </div>
    @endif
</div>

{{-- Investor-Student Interaction Tracking --}}
<div class="admin-activity-card">
    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fa-solid fa-handshake" style="color:var(--accent-soft);"></i> Investor-Student Interactions
    </h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem;text-align:center;">
            <div style="font-size:1.75rem;font-weight:700;color:var(--text);">${{ number_format($totalInvestmentAmount) }}</div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Total Invested</div>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem;text-align:center;">
            <div style="font-size:1.75rem;font-weight:700;color:var(--text);">{{ $activeInvestors }}</div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Active Investors</div>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem;text-align:center;">
            <div style="font-size:1.75rem;font-weight:700;color:var(--text);">{{ $activeFounders }}</div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Active Founders</div>
        </div>
    </div>
    @if(count($recentInteractions))
        <div style="display:flex;flex-direction:column;">
            @foreach($recentInteractions as $interaction)
                <div class="admin-activity-item">
                    <div class="admin-activity-avatar">
                        {{ substr($interaction->investor?->name ?? 'I', 0, 1) }}
                    </div>
                    <div class="admin-activity-time">{{ $interaction->created_at?->diffForHumans() }}</div>
                    <div style="flex:1;">
                        <span class="admin-activity-user">{{ $interaction->investor?->name ?? 'Investor' }}</span>
                        <span class="admin-activity-action">
                            invested ${{ number_format($interaction->amount) }} in
                            <a href="{{ route('startups.show', $interaction->startup) }}" style="color:var(--primary);text-decoration:none;">{{ $interaction->startup?->name ?? 'Startup' }}</a>
                            by {{ $interaction->startup?->founder?->name ?? 'Founder' }}
                            ({{ ucfirst($interaction->status) }})
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.9rem;">
            <i class="fa-solid fa-handshake" style="font-size:2rem;margin-bottom:0.75rem;opacity:0.5;"></i>
            <p>No investor-student interactions yet.</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        const textColor = isDark ? '#f5f1ec' : '#1a1a1a';
        const gridColor = isDark ? 'rgba(127, 163, 154, 0.15)' : 'rgba(24, 67, 67, 0.12)';

        Chart.defaults.color = textColor;
        Chart.defaults.borderColor = gridColor;

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {{ $userGrowthData->pluck('date') }},
                datasets: [{
                    label: 'New Users',
                    data: {{ $userGrowthData->pluck('count') }},
                    borderColor: '#7fa39a',
                    backgroundColor: 'rgba(127, 163, 154, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Startup Stage Chart
        const startupStageCtx = document.getElementById('startupStageChart').getContext('2d');
        new Chart(startupStageCtx, {
            type: 'doughnut',
            data: {
                labels: {{ $startupStageData->pluck('stage') }},
                datasets: [{
                    data: {{ $startupStageData->pluck('count') }},
                    backgroundColor: ['#7fa39a', '#c89b5d', '#5c8f6a'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Role Distribution Chart
        const roleDistributionCtx = document.getElementById('roleDistributionChart').getContext('2d');
        new Chart(roleDistributionCtx, {
            type: 'pie',
            data: {
                labels: {{ $roleDistributionData->pluck('role') }},
                datasets: [{
                    data: {{ $roleDistributionData->pluck('count') }},
                    backgroundColor: ['#b46a6a', '#7fa39a', '#5c8f6a', '#c89b5d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Investment Trend Chart
        const investmentTrendCtx = document.getElementById('investmentTrendChart').getContext('2d');
        new Chart(investmentTrendCtx, {
            type: 'bar',
            data: {
                labels: {{ $investmentTrendData->pluck('date') }},
                datasets: [{
                    label: 'Investments',
                    data: {{ $investmentTrendData->pluck('count') }},
                    backgroundColor: '#184343',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
@endsection