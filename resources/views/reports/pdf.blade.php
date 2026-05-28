<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — Invesmal</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 40px; }
        h1 { color: #184343; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .meta { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="meta">Generated {{ $generated_at }} for {{ $user->name }} ({{ ucwords(str_replace('_', ' ', $user->role)) }})</p>

    <table>
        <tr><th>Metric</th><th>Value</th></tr>
        @if(isset($total_users))
            <tr><td>Total Users</td><td>{{ $total_users }}</td></tr>
        @endif
        @if(isset($total_startups))
            <tr><td>Total Startups</td><td>{{ $total_startups }}</td></tr>
        @endif
        @if(isset($total_investments))
            <tr><td>Total Investments</td><td>{{ $total_investments }}</td></tr>
        @endif
        @if(isset($total_raised))
            <tr><td>Total Raised</td><td>${{ number_format($total_raised) }}</td></tr>
        @endif
        @if(isset($approved))
            <tr><td>Approved Investments</td><td>{{ $approved }}</td></tr>
        @endif
        @if(isset($pending))
            <tr><td>Pending Investments</td><td>{{ $pending }}</td></tr>
        @endif
    </table>

    @if(isset($by_stage) && $by_stage->isNotEmpty())
        <h2>By Stage</h2>
        <table>
            <tr><th>Stage</th><th>Count</th></tr>
            @foreach($by_stage as $row)
                <tr><td>{{ ucfirst($row->stage) }}</td><td>{{ $row->count }}</td></tr>
            @endforeach
        </table>
    @endif

    <p class="meta" style="margin-top:40px;">Invesmal — Startup Investment Platform</p>
</body>
</html>
