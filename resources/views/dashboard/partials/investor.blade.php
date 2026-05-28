{{-- Investor dashboard --}}
<div class="investor-dash">
    <div class="investor-stats-grid">
        <div class="modern-card stat-card">
            <div class="stat-header"><i class="fa-solid fa-briefcase"></i><span>Portfolio</span></div>
            <div class="stat-value">{{ $myInvestments ?? 0 }}</div>
            <div class="stat-label">Total offers</div>
        </div>
        <div class="modern-card stat-card">
            <div class="stat-header"><i class="fa-solid fa-hourglass-half"></i><span>Pending</span></div>
            <div class="stat-value">{{ $pendingInvestments ?? 0 }}</div>
            <div class="stat-label">Awaiting founder</div>
        </div>
        <div class="modern-card stat-card">
            <div class="stat-header"><i class="fa-solid fa-circle-check"></i><span>Approved</span></div>
            <div class="stat-value">{{ $approvedInvestments ?? 0 }}</div>
            <div class="stat-label">Active deals</div>
        </div>
        <div class="modern-card stat-card">
            <div class="stat-header"><i class="fa-solid fa-dollar-sign"></i><span>Committed</span></div>
            <div class="stat-value">${{ number_format($totalInvested ?? 0) }}</div>
            <div class="stat-label">Approved amount</div>
        </div>
    </div>

    <div class="modern-card invest-flow-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-route icon"></i> How to invest on Invesmal</h3>
        </div>
        <div class="card-body">
            <div class="invest-steps">
                <div class="invest-step">
                    <span class="invest-step-num">1</span>
                    <div>
                        <strong>Discover startups</strong>
                        <p>Browse by category — FinTech, AgriTech, HealthTech, and more.</p>
                        <a href="{{ route('startups.discover') }}" class="invest-step-link">Go to Discover <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="invest-step">
                    <span class="invest-step-num">2</span>
                    <div>
                        <strong>Open a startup profile</strong>
                        <p>Read mission, traction, funding goal, and pitch deck summary.</p>
                    </div>
                </div>
                <div class="invest-step">
                    <span class="invest-step-num">3</span>
                    <div>
                        <strong>Submit investment offer</strong>
                        <p>Enter amount (USD) and a message to the founder.</p>
                        <a href="{{ route('investments.create') }}" class="invest-step-link">New investment offer <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="invest-step">
                    <span class="invest-step-num">4</span>
                    <div>
                        <strong>Founder reviews</strong>
                        <p>Track status: Pending → Approved or Rejected in My Investments.</p>
                        <a href="{{ route('investments.index') }}" class="invest-step-link">My Investments <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="invest-step">
                    <span class="invest-step-num">5</span>
                    <div>
                        <strong>Connect & close</strong>
                        <p>Message the founder or schedule a meeting from the startup page.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(($recentInvestments ?? collect())->isNotEmpty())
    <div class="modern-card" style="margin-top:1.5rem;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left icon"></i> Recent investment activity</h3>
            <a href="{{ route('investments.index') }}" class="panel-link">View all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Startup</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentInvestments as $inv)
                    <tr>
                        <td>
                            <a href="{{ route('startups.show', $inv->startup) }}" style="font-weight:600;color:var(--text);">{{ $inv->startup?->name }}</a>
                        </td>
                        <td>${{ number_format($inv->amount) }}</td>
                        <td>
                            <span class="stage-badge stage-{{ $inv->status === 'approved' ? 'funded' : ($inv->status === 'pending' ? 'mvp' : 'idea') }}">
                                {{ ucfirst($inv->status) }}
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:0.85rem;">{{ $inv->created_at->diffForHumans() }}</td>
                        <td><a href="{{ route('investments.show', $inv) }}" class="panel-link">Details</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="modern-card" style="margin-top:1.5rem;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-layer-group icon"></i> Explore by category</h3>
            <a href="{{ route('startups.discover') }}" class="panel-link">Full discover <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            @include('startups.partials.by-industry', ['startupsByIndustry' => $startupsByIndustry ?? []])
        </div>
    </div>
</div>
