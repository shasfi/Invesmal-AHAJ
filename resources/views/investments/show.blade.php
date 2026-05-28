@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/investments/investments.css',
    ]])
@endpush

@section('title', 'Investment — ' . ($investment->startup?->name ?? 'Invesmal'))

@section('content')
@php
    $statusColors = ['pending' => 'stage-mvp', 'approved' => 'stage-funded', 'rejected' => 'stage-idea'];
    $statusLabels = ['pending' => 'Pending founder review', 'approved' => 'Approved', 'rejected' => 'Declined'];
    $isInvestor = auth()->id() === $investment->investor_id;
    $isFounder = auth()->id() === ($investment->startup?->founder_id);
    $canReview = $isFounder && $investment->status === 'pending';
@endphp

<div class="investment-detail-page">
    <header class="dashboard-topbar" style="margin-bottom:1.5rem;">
        <a href="{{ route('investments.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </header>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">
        <div class="modern-card">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
                    <div>
                        <span class="stage-badge {{ $statusColors[$investment->status] ?? '' }}" style="font-size:0.8rem;">
                            {{ $statusLabels[$investment->status] ?? $investment->status }}
                        </span>
                        <h1 style="font-size:1.5rem;margin:0.75rem 0 0;">{{ $investment->startup?->name }}</h1>
                    </div>
                    @if($investment->amount)
                        <div style="text-align:right;">
                            <div style="font-size:0.8rem;color:var(--muted);">Offer amount</div>
                            <div style="font-size:2rem;font-weight:800;">${{ number_format($investment->amount) }}</div>
                        </div>
                    @endif
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                    <div style="padding:1rem;background:var(--surface);border-radius:var(--radius-md);border:1px solid var(--border);">
                        <div style="font-size:0.75rem;color:var(--muted);margin-bottom:0.25rem;">Investor</div>
                        <div style="font-weight:600;">{{ $investment->investor->name }}</div>
                        <div style="font-size:0.85rem;color:var(--muted);">{{ $investment->investor->email }}</div>
                    </div>
                    <div style="padding:1rem;background:var(--surface);border-radius:var(--radius-md);border:1px solid var(--border);">
                        <div style="font-size:0.75rem;color:var(--muted);margin-bottom:0.25rem;">Founder</div>
                        <div style="font-weight:600;">{{ $investment->startup?->founder?->name }}</div>
                        <a href="{{ route('startups.show', $investment->startup) }}" style="font-size:0.85rem;">View startup profile</a>
                    </div>
                </div>

                @if($investment->message)
                <div style="margin-bottom:1.5rem;">
                    <h4 style="font-size:0.85rem;color:var(--muted);margin-bottom:0.5rem;">Message from investor</h4>
                    <p style="line-height:1.7;color:var(--text-secondary);white-space:pre-wrap;">{{ $investment->message }}</p>
                </div>
                @endif

                @if($investment->admin_remarks)
                <div style="margin-bottom:1.5rem;padding:1rem;background:var(--surface);border-radius:var(--radius-md);">
                    <h4 style="font-size:0.85rem;color:var(--muted);margin-bottom:0.5rem;">Founder / admin notes</h4>
                    <p style="margin:0;">{{ $investment->admin_remarks }}</p>
                </div>
                @endif

                <div style="font-size:0.85rem;color:var(--muted);">
                    <p><i class="fa-solid fa-clock"></i> Submitted {{ $investment->created_at->format('M j, Y g:i A') }}</p>
                    @if($investment->reviewed_at)
                        <p><i class="fa-solid fa-check"></i> Reviewed {{ $investment->reviewed_at->format('M j, Y g:i A') }}</p>
                    @endif
                </div>

                @if($canReview)
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);">
                    <h3 style="font-size:1rem;margin-bottom:1rem;">Founder decision</h3>
                    <form action="{{ route('investments.approve', $investment) }}" method="POST" style="margin-bottom:1rem;">
                        @csrf @method('PATCH')
                        <textarea name="admin_remarks" rows="2" class="form-textarea" placeholder="Optional note to investor (e.g. welcome aboard)"></textarea>
                        <button type="submit" class="btn-primary" style="margin-top:0.5rem;">
                            <i class="fa-solid fa-check"></i> Approve investment
                        </button>
                    </form>
                    <form action="{{ route('investments.reject', $investment) }}" method="POST">
                        @csrf @method('PATCH')
                        <textarea name="admin_remarks" rows="2" class="form-textarea" placeholder="Reason for declining (optional)"></textarea>
                        <button type="submit" class="btn-secondary" style="margin-top:0.5rem;color:var(--danger);border-color:rgba(180,106,106,0.4);">
                            <i class="fa-solid fa-xmark"></i> Decline offer
                        </button>
                    </form>
                </div>
                @endif

                @if(auth()->user()->role === 'admin' && $investment->status === 'pending')
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);">
                    <h3 style="font-size:1rem;margin-bottom:1rem;">Admin override</h3>
                    <form action="{{ route('investments.approve', $investment) }}" method="POST" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary">Approve</button>
                    </form>
                    <form action="{{ route('investments.reject', $investment) }}" method="POST" style="display:inline;margin-left:0.5rem;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-secondary">Reject</button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <aside style="display:flex;flex-direction:column;gap:1rem;">
            @if($investment->startup)
            <div class="modern-card">
                <div class="card-header"><h3 class="card-title" style="font-size:0.95rem;">Startup</h3></div>
                <div class="card-body">
                    <a href="{{ route('startups.show', $investment->startup) }}" style="display:flex;gap:0.75rem;align-items:center;text-decoration:none;color:inherit;">
                        <img src="{{ $investment->startup->logo_url }}" alt="" style="width:48px;height:48px;border-radius:var(--radius-md);">
                        <div>
                            <strong>{{ $investment->startup->name }}</strong>
                            <div style="font-size:0.8rem;color:var(--muted);">{{ $investment->startup->industry }}</div>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            <div class="modern-card">
                <div class="card-header"><h3 class="card-title" style="font-size:0.95rem;">Next steps</h3></div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:0.5rem;">
                    @if($isInvestor && $investment->status === 'pending')
                        <p style="font-size:0.85rem;color:var(--muted);margin:0 0 0.5rem;">Waiting for the founder to approve or decline your offer.</p>
                    @endif
                    @if($isInvestor && $investment->status === 'approved')
                        <p style="font-size:0.85rem;color:var(--success);margin:0 0 0.5rem;"><i class="fa-solid fa-circle-check"></i> Deal approved — connect with the founder below.</p>
                    @endif
                    <form action="{{ route('conversations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="participant_id" value="{{ $isInvestor ? $investment->startup?->founder_id : $investment->investor_id }}">
                        <input type="hidden" name="subject" value="Investment: {{ $investment->startup?->name }}">
                        <button type="submit" class="btn-secondary" style="width:100%;">
                            <i class="fa-solid fa-comment"></i> Send message
                        </button>
                    </form>
                    @if($investment->startup)
                    <a href="{{ route('meetings.create', ['startup_id' => $investment->startup_id, 'invitee_id' => $isInvestor ? $investment->startup->founder_id : $investment->investor_id]) }}" class="btn-secondary" style="text-align:center;">
                        <i class="fa-solid fa-calendar-plus"></i> Schedule meeting
                    </a>
                    @endif
                    @if($isInvestor)
                    <a href="{{ route('startups.discover') }}" class="btn-ghost" style="text-align:center;font-size:0.85rem;">Discover more startups</a>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
