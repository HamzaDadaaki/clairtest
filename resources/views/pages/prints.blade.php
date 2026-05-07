@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Prints'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Prints</span>
        <h1>Fine art print products ready to order.</h1>
        <p>This page is dedicated to prints only, with each print having the same clean product presentation as original artworks.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="pod-grid prints-page-grid">
            @forelse($printfulProducts as $product)
                <article class="pod-card reveal-up printful-product-card">
                    @if(!empty($product['images'][0]))
                        <img src="{{ str_starts_with($product['images'][0], 'http') ? $product['images'][0] : asset($product['images'][0]) }}" alt="{{ $product['name'] }}">
                    @endif
                    <div class="print-card-body">
                        <h3>{{ $product['name'] }}</h3>
                        @if(!empty($product['description']))
                            <p class="print-desc">{{ $product['description'] }}</p>
                        @endif
                        <div class="print-price-row">
                            <strong class="print-price">{{ $product['price'] }}</strong>
                            <a class="button button-secondary" href="{{ route('products.show', $product['slug']) }}">View piece</a>
                            <form method="post" action="{{ route('cart.add_printful', $product['printful_id']) }}">
                                @csrf
                                <button type="submit" class="button">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Art prints</h3><p>Print products will appear here after products are synced from the admin panel.</p></article>
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Stickers</h3><p>Small colorful products for gifts, packaging, and everyday use.</p></article>
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Bags</h3><p>Tote bags and practical items with Claire’s artwork.</p></article>
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Gift items</h3><p>Cards, accessories, and seasonal products can be added later.</p></article>
            @endforelse
        </div>
    </div>
</section>
@endsection
