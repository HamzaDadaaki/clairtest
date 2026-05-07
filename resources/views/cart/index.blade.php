@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Cart'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Cart</span>
        <h1>Your selected artwork and print products.</h1>
        <p>Original artworks, local shop products, and Printful prints can be reviewed here before checkout.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        @if (count($items))
            <div class="cart-list claire-cart-list">
                @foreach ($items as $item)
                    <article class="cart-row glass-shell reveal-up">
                        <div class="cart-row-image" @if($item['image']) style="background-image:url('{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset($item['image']) }}')" @endif></div>
                        <div>
                            <span class="tag">{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</span>
                            <h2>{{ $item['name'] }}</h2>
                            <p>{{ $item['description'] }}</p>
                            <strong>{{ $item['display_price'] }}</strong>
                        </div>
                        <div class="cart-actions">
                            <a href="{{ $item['url'] }}" class="button button-secondary button-sm">View</a>
                            <form action="{{ route('cart.remove', $item['slug']) }}" method="POST">
                                @csrf
                                <button type="submit" class="button button-secondary button-sm">Remove</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="cart-footer-actions reveal-up">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="button button-secondary">Clear cart</button>
                </form>
                <div class="cart-total-box"><span>Estimated subtotal</span><strong>{{ number_format($subtotal, 2) }}</strong></div>
                <a href="{{ route('checkout.show') }}" class="button">Proceed to checkout</a>
            </div>
        @else
            <div class="empty-state glass-shell reveal-up">
                <h2>Your cart is empty.</h2>
                <p>Browse originals, prints, or commission examples to start.</p>
                <a href="{{ route('products.index') }}" class="button">Browse arts/shop</a>
            </div>
        @endif
    </div>
</section>
@endsection
