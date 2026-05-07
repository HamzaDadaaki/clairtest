@extends('layouts.admin', ['title' => 'Admin Dashboard | Afayar'])

@section('content')
<section class="admin-panel-card dashboard-header-card">
    <div>
        <span class="eyebrow">Overview</span>
        <h1>Website admin dashboard</h1>
        <p>From here you can manage the products, partners, website traffic, and all contact leads sent from the website.</p>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('admin.products.create') }}" class="admin-primary-button">Add new product</a>
        <a href="{{ route('admin.partners.index') }}" class="admin-link-button">Manage partners</a>
    </div>
</section>

<section class="admin-stats-grid four">
    <article class="admin-stat-card">
        <strong>{{ count($products) }}</strong>
        <span>Total products</span>
    </article>
    <article class="admin-stat-card">
        <strong>{{ count($partners) }}</strong>
        <span>Partners</span>
    </article>
    <article class="admin-stat-card">
        <strong>{{ $analytics['visits'] }}</strong>
        <span>Tracked visits</span>
    </article>
    <article class="admin-stat-card">
        <strong>{{ count($contacts) }}</strong>
        <span>Contact messages</span>
    </article>
</section>

<section class="admin-stats-grid four">
    <article class="admin-stat-card compact">
        <strong>{{ $analytics['clicks'] }}</strong>
        <span>Total clicks</span>
    </article>
    <article class="admin-stat-card compact">
        <strong>{{ $analytics['bounce_rate'] }}%</strong>
        <span>Bounce rate</span>
    </article>
    <article class="admin-stat-card compact">
        <strong>{{ $analytics['conversion_rate'] }}%</strong>
        <span>Conversion rate</span>
    </article>
    <article class="admin-stat-card compact">
        <strong>{{ $analytics['page_views'] }}</strong>
        <span>Page views</span>
    </article>
</section>

<section class="admin-grid two dashboard-overview-grid">
    <article class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <span class="eyebrow">Analytics</span>
                <h2>Traffic overview</h2>
            </div>
            <a href="{{ route('admin.analytics.index') }}" class="admin-link-button small">Open analytics</a>
        </div>

        <div class="mini-breakdown-grid">
            <div>
                <h3>Sources</h3>
                <ul class="admin-mini-list">
                    @forelse (array_slice($analytics['source_breakdown'], 0, 5, true) as $label => $value)
                        <li><span>{{ $label }}</span><strong>{{ $value }}</strong></li>
                    @empty
                        <li><span>No traffic yet</span><strong>0</strong></li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3>Devices</h3>
                <ul class="admin-mini-list">
                    @forelse ($analytics['device_breakdown'] as $label => $value)
                        <li><span>{{ ucfirst($label) }}</span><strong>{{ $value }}</strong></li>
                    @empty
                        <li><span>No data yet</span><strong>0</strong></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <span class="eyebrow">Inbox</span>
                <h2>Latest contact messages</h2>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="admin-link-button small">Open inbox</a>
        </div>

        <div class="admin-message-stack">
            @forelse (array_slice($contacts, 0, 4) as $message)
                <a href="{{ route('admin.contacts.show', $message['id']) }}" class="admin-message-card-link">
                    <article class="admin-message-card">
                        <div class="admin-message-topline">
                            <strong>{{ $message['name'] ?: 'Unknown sender' }}</strong>
                            <span class="status-pill status-{{ $message['status'] }}">{{ str_replace('_', ' ', $message['status']) }}</span>
                        </div>
                        <p>{{ $message['subject'] }}</p>
                        <small>{{ $message['email'] }} · {{ \Illuminate\Support\Str::limit($message['created_at'], 16, '') }}</small>
                    </article>
                </a>
            @empty
                <p class="empty-state">No contact messages yet.</p>
            @endforelse
        </div>
    </article>
</section>

<section class="admin-grid two dashboard-overview-grid">
    <article class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <span class="eyebrow">Products</span>
                <h2>Recent products</h2>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-link-button small">Manage products</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table compact-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($products, 0, 5) as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['type'] }}</td>
                            <td>{{ $product['price_label'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <span class="eyebrow">Partners</span>
                <h2>Current partners</h2>
            </div>
            <a href="{{ route('admin.partners.index') }}" class="admin-link-button small">Manage partners</a>
        </div>
        <div class="partner-admin-grid compact">
            @forelse (array_slice($partners, 0, 6) as $partner)
                <div class="partner-admin-card">
                    <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }} logo">
                    <strong>{{ $partner['name'] }}</strong>
                    <span>{{ $partner['tagline'] ?: 'Partner logo' }}</span>
                </div>
            @empty
                <p class="empty-state">No partners yet.</p>
            @endforelse
        </div>
    </article>
</section>
@endsection
