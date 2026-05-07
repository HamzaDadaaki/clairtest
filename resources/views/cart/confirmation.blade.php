@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Order Confirmation'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Order received</span>
        <h1>Thank you for your order.</h1>
        <p>The order is saved inside Claire’s admin panel with payment and fulfillment status.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="confirmation-card glass-shell reveal-up claire-confirmation-card">
            <h2>Order {{ $order['id'] }}</h2>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($order['date'])->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Payment status:</strong> {{ ucfirst(str_replace('_', ' ', $order['payment_status'])) }}</p>
            <p><strong>Fulfillment status:</strong> {{ ucfirst(str_replace('_', ' ', $order['fulfillment_status'])) }}</p>

            <hr>

            <h3>Customer</h3>
            <p>
                <strong>{{ $order['customer']['first_name'] ?? '' }} {{ $order['customer']['last_name'] ?? '' }}</strong><br>
                {{ $order['customer']['email'] ?? '' }} · {{ $order['customer']['phone'] ?? '' }}<br>
                {{ $order['customer']['address'] ?? '' }}, {{ $order['customer']['city'] ?? '' }}, {{ $order['customer']['postal_code'] ?? '' }}, {{ $order['customer']['country'] ?? '' }}
            </p>

            <h3>Items</h3>
            <div class="confirmation-items">
                @foreach ($order['items'] as $item)
                    <div class="confirmation-item">
                        <div>
                            <h4>{{ $item['name'] }}</h4>
                            <p>{{ ucfirst(str_replace('_', ' ', $item['type'] ?? 'item')) }}</p>
                        </div>
                        <strong>{{ $item['display_price'] ?? $item['price'] ?? 'Custom quote' }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="order-total-section">
                <span>Estimated subtotal:</span>
                <strong>{{ number_format((float) $order['subtotal'], 2) }}</strong>
            </div>

            @if(!empty($order['customer']['message']))
                <h3>Special note</h3>
                <p>{{ $order['customer']['message'] }}</p>
            @endif

            <div class="confirmation-actions">
                <a href="{{ route('home') }}" class="button">Back to home</a>
                <a href="{{ route('products.index') }}" class="button button-secondary">Continue shopping</a>
            </div>
        </div>
    </div>
</section>
@endsection
