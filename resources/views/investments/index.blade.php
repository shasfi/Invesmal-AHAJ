@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/investments/investments.css',
    ]])
@endpush

@section('title', 'My Investments — Invesmal')

@section('content')
<div class="investments-page">
    <header class="dashboard-topbar">
        <div class="topbar-left">
            <h1 class="topbar-title">
                @if(auth()->user()->role === 'investor')
                    My Investments
                @elseif(auth()->user()->role === 'student_founder')
                    Investment offers
                @else
                    Investments
                @endif
            </h1>
            <p class="topbar-subtitle">
                @if(auth()->user()->role === 'investor')
                    Track every offer you sent — pending, approved, or declined
                @elseif(auth()->user()->role === 'student_founder')
                    Investors interested in your startups — approve or decline each offer
                @else
                    Platform investment activity
                @endif
            </p>
        </div>
        @if(auth()->user()->role === 'investor')
        <div class="topbar-right">
            <a href="{{ route('startups.discover') }}" class="btn-secondary"><i class="fa-solid fa-compass"></i> Discover</a>
            <a href="{{ route('investments.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New offer</a>
        </div>
        @endif
    </header>

    {{-- Investors Section for Students --}}
    @if(auth()->user()->role === 'student_founder' && $investors->isNotEmpty())
    <div class="modern-card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-users"></i> Investors in Your Startups</h2>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
                @foreach($investors as $investor)
                <div class="modern-card card-hover-lift" style="padding:1.25rem;">
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:var(--text);font-weight:600;font-size:1.25rem;">
                            {{ substr($investor->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 style="margin:0;font-size:1rem;font-weight:600;">{{ $investor->name }}</h3>
                            <span style="font-size:0.8rem;color:var(--muted);">Investor</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:0.5rem;">
                        <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;color:var(--text-secondary);">
                            <i class="fa-solid fa-envelope" style="width:16px;"></i>
                            {{ $investor->email }}
                        </div>
                        <a href="{{ route('users.profile', $investor) }}" class="btn-secondary" style="margin-top:0.5rem;font-size:0.8rem;padding:0.5rem 0.75rem;">
                            <i class="fa-solid fa-eye"></i> View Profile
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @php
        $statusColors = [
            'pending' => 'stage-mvp',
            'approved' => 'stage-funded',
            'rejected' => 'stage-idea',
        ];
        $statusLabels = [
            'pending' => 'Pending — awaiting founder',
            'approved' => 'Approved',
            'rejected' => 'Declined',
        ];
    @endphp

    @if($investments->isEmpty())
        <div class="modern-card">
            <div class="card-body" style="text-align:center;padding:3rem 2rem;">
                <i class="fa-solid fa-hand-holding-dollar" style="font-size:2.5rem;color:var(--muted);margin-bottom:1rem;"></i>
                <h3 style="margin-bottom:0.5rem;">No investments yet</h3>
                @if(auth()->user()->role === 'investor')
                    <p style="color:var(--muted);max-width:400px;margin:0 auto 1.5rem;">Browse startups, open a profile, and click <strong>Invest</strong> to send your first offer.</p>
                    <a href="{{ route('startups.discover') }}" class="btn-primary"><i class="fa-solid fa-compass"></i> Discover startups</a>
                @else
                    <p style="color:var(--muted);">Offers from investors will appear here.</p>
                @endif
            </div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:1rem;">
            @foreach($investments as $inv)
                <a href="{{ route('investments.show', $inv) }}" class="modern-card" style="text-decoration:none;color:inherit;display:block;">
                    <div class="card-body" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;">
                        <div style="display:flex;gap:1rem;align-items:center;min-width:200px;">
                            @if($inv->startup)
                                <img src="{{ $inv->startup->logo_url }}" alt="" style="width:48px;height:48px;border-radius:var(--radius-md);object-fit:cover;">
                            @endif
                            <div>
                                <h3 style="margin:0 0 0.25rem;font-size:1.05rem;">{{ $inv->startup?->name ?? 'Startup' }}</h3>
                                <p style="margin:0;font-size:0.85rem;color:var(--muted);">
                                    @if(auth()->user()->role === 'investor')
                                        Founder: {{ $inv->startup?->founder?->name }}
                                    @else
                                        Investor: {{ $inv->investor?->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:1.5rem;">
                            @if($inv->amount)
                                <span style="font-size:1.25rem;font-weight:700;">${{ number_format($inv->amount) }}</span>
                            @endif
                            <span class="stage-badge {{ $statusColors[$inv->status] ?? '' }}">
                                {{ $statusLabels[$inv->status] ?? $inv->status }}
                            </span>
                            <span style="color:var(--muted);font-size:0.8rem;">{{ $inv->created_at->diffForHumans() }}</span>
                            <i class="fa-solid fa-chevron-right" style="color:var(--muted);"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($investments->hasPages())
            <div style="margin-top:1.5rem;">{{ $investments->links() }}</div>
        @endif
    @endif
</div>
@endsection
