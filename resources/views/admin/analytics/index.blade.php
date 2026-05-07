@extends('layouts.admin', ['title' => 'Website Analytics | Afayar'])

@section('content')
<section class="admin-panel-card dashboard-header-card">
    <div>
        <span class="eyebrow">Analytics</span>
        <h1>Website traffic and behavior</h1>
        <p>Track visits, clicks, bounce rate, conversion rate, entry source, device type, and recent visitor sessions directly from the admin panel.</p>
    </div>
</section>

<section class="admin-stats-grid four">
    <article class="admin-stat-card"><strong>{{ $analytics['visits'] }}</strong><span>Visits</span></article>
    <article class="admin-stat-card"><strong>{{ $analytics['clicks'] }}</strong><span>Clicks</span></article>
    <article class="admin-stat-card"><strong>{{ $analytics['bounce_rate'] }}%</strong><span>Bounce rate</span></article>
    <article class="admin-stat-card"><strong>{{ $analytics['conversion_rate'] }}%</strong><span>Conversion rate</span></article>
</section>

<section class="admin-grid two">
    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Sources</span>
            <h2>Where visitors are coming from</h2>
        </div>
        <ul class="admin-breakdown-list">
            @forelse ($analytics['source_breakdown'] as $label => $value)
                <li><span>{{ ucfirst($label) }}</span><strong>{{ $value }}</strong></li>
            @empty
                <li><span>No source data yet</span><strong>0</strong></li>
            @endforelse
        </ul>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Devices</span>
            <h2>Phone or desktop</h2>
        </div>
        <ul class="admin-breakdown-list">
            @forelse ($analytics['device_breakdown'] as $label => $value)
                <li><span>{{ ucfirst($label) }}</span><strong>{{ $value }}</strong></li>
            @empty
                <li><span>No device data yet</span><strong>0</strong></li>
            @endforelse
        </ul>
    </article>
</section>

<section class="admin-grid two">
    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Entry pages</span>
            <h2>Top pages people start on</h2>
        </div>
        <ul class="admin-breakdown-list">
            @forelse (array_slice($analytics['entry_pages'], 0, 10, true) as $label => $value)
                <li><span>{{ $label }}</span><strong>{{ $value }}</strong></li>
            @empty
                <li><span>No page data yet</span><strong>0</strong></li>
            @endforelse
        </ul>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Action clicks</span>
            <h2>Most clicked buttons and links</h2>
        </div>
        <ul class="admin-breakdown-list">
            @forelse (array_slice($analytics['cta_breakdown'], 0, 10, true) as $label => $value)
                <li><span>{{ $label }}</span><strong>{{ $value }}</strong></li>
            @empty
                <li><span>No click data yet</span><strong>0</strong></li>
            @endforelse
        </ul>
    </article>
</section>

<section class="admin-panel-card">
    <div class="admin-section-head inline">
        <div>
            <span class="eyebrow">Recent visitors</span>
            <h2>Latest tracked sessions</h2>
        </div>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Started</th>
                    <th>Entry page</th>
                    <th>Source</th>
                    <th>Device</th>
                    <th>Clicks</th>
                    <th>Conversions</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($analytics['recent_sessions'] as $session)
                    <tr>
                        <td>{{ $session['started_at'] }}</td>
                        <td><code>{{ $session['entry_page'] }}</code></td>
                        <td>{{ ucfirst($session['source']) }}</td>
                        <td>{{ ucfirst($session['device']) }}</td>
                        <td>{{ $session['clicks'] }}</td>
                        <td>{{ $session['conversions'] }}</td>
                        <td>{{ $session['max_duration_seconds'] }}s</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No visits tracked yet. After people start using the website, data will appear here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
