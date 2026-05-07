@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Checkout'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Checkout</span>
        <h1>Complete your order.</h1>
        <p>Your order will be saved in the admin panel. If Stripe keys are configured, customers will be redirected to Stripe checkout.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="checkout-wrapper claire-checkout-wrapper">
            <div class="order-summary-section glass-shell reveal-up">
                <h2>Order Summary</h2>
                <div class="order-items">
                    @foreach ($items as $item)
                        <div class="order-item">
                            <div>
                                <h3>{{ $item['name'] }}</h3>
                                <p class="item-type">{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</p>
                            </div>
                            <div class="item-price">{{ $item['display_price'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="order-total">
                    <span>Estimated subtotal:</span>
                    <strong>{{ number_format($subtotal, 2) }}</strong>
                </div>
                @unless($stripeReady)
                    <p class="integration-warning">Stripe key is missing in .env, so this will save the order locally instead of taking live card payment.</p>
                @endunless
            </div>
            <form action="{{ route('checkout.process') }}" method="POST" class="checkout-form glass-shell reveal-up">
                @csrf
                <h2>Customer information</h2>
                <div class="selected-products-box">
                    <h3>Selected products</h3>
                    <ul>
                        @foreach ($items as $item)
                            <li>
                                <span>{{ $item['name'] }}</span>
                                <strong>{{ $item['display_price'] }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="admin-form-grid two-col-form">
                    <div><label>First name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" required></div>
                    <div><label>Last name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" required></div>
                    <div><label>Email *</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                    <div><label>Phone *</label><input type="text" name="phone" value="{{ old('phone') }}" required></div>
                </div>

                <h2>Shipping address</h2>
                <label>Street address *</label>
                <input type="text" name="address" value="{{ old('address') }}" required>
                <div class="admin-form-grid three-col-form">
                    <div><label>City *</label><input type="text" name="city" value="{{ old('city') }}" required></div>
                    <div><label>Postal code *</label><input type="text" name="postal_code" value="{{ old('postal_code') }}" required></div>
                    <div><label>Country *</label><input type="text" name="country" value="{{ old('country') }}" required></div>
                </div>

                <label>Special note</label>
                <textarea name="message" rows="4">{{ old('message') }}</textarea>

                <div class="checkout-actions">
                    <a href="{{ route('cart.index') }}" class="button button-secondary">Back to cart</a>
                    <button type="submit" class="button">{{ $stripeReady ? 'Pay with Stripe' : 'Save order' }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
