@extends('layouts.app', ['title' => 'Claire Stefanich Arts | ' . $product['name']])

@section('content')
@php
    $galleryImages = array_values(array_filter($product['images'] ?? []));
    $mainImage = $galleryImages[0] ?? null;
    $mainImageUrl = $mainImage ? (str_starts_with($mainImage, 'http') ? $mainImage : asset($mainImage)) : null;
    $isPrintProduct = ($product['category'] ?? '') === 'print' && !empty($product['printful_id']);
@endphp
<section class="section product-single">
    <div class="container product-single-grid">
        <div>
            <div class="gallery-container">
                <div class="gallery-main reveal-up" @if($mainImageUrl) style="background-image:url('{{ $mainImageUrl }}')" @endif data-current="0" data-images='@json(array_map(fn ($img) => str_starts_with($img, "http") ? $img : asset($img), $galleryImages))'>
                    @if(count($galleryImages) > 1)
                        <button class="gallery-nav gallery-prev" aria-label="Previous image">&larr;</button>
                        <button class="gallery-nav gallery-next" aria-label="Next image">&rarr;</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="product-details reveal-up">
            <span class="eyebrow">{{ $product['tag'] }}</span>
            <h1>{{ $product['name'] }}</h1>
            <div class="product-status-row">
                <div class="price">{{ $product['price'] }}</div>
                <span class="art-status-badge {{ $product['status'] === 'sold' ? 'sold' : 'for-sale' }}">{{ $product['status'] === 'sold' ? 'Sold' : ucfirst(str_replace('_', ' ', $product['status'])) }}</span>
            </div>
            <p>{{ $product['description'] }}</p>

            <div class="product-facts-grid">
                <article class="product-fact-card">
                    <span class="eyebrow">Size</span>
                    <h3>Artwork format</h3>
                    <p>{{ $product['size'] ?: 'Available on request' }}</p>
                </article>
                <article class="product-fact-card story-behind-card">
                    <span class="eyebrow">Story behind it</span>
                    <h3>Why this piece exists</h3>
                    <p>{{ $product['story'] ?: 'Each work is part observation, part memory, and part handmade atmosphere.' }}</p>
                </article>
            </div>

            @if($product['category'] === 'commission_example')
                <a class="button" href="{{ route('commissions') }}">Order a commission like this</a>
            @elseif($product['status'] === 'sold')
                <div class="sold-notice">This original has been sold and is shown as a collected piece. Visitors can still request a similar custom commission.</div>
                <a class="button" href="{{ route('commissions') }}">Request a similar commission</a>
            @else
                <div class="product-action-row product-action-row-centered">
                    <form method="post" action="{{ $isPrintProduct ? route('cart.add_printful', $product['printful_id']) : route('cart.add', $product['slug']) }}" class="product-action-form">
                        @csrf
                        <button type="submit" class="button">Add to cart</button>
                    </form>
                    <a class="button button-secondary" href="#product-inquiry">Ask for buying</a>
                </div>

                <form method="post" action="{{ route('products.inquire', $product['slug']) }}" class="inquiry-form" id="product-inquiry">
                    @csrf
                    <input type="text" name="name" placeholder="Your name" required>
                    <input type="email" name="email" placeholder="Your email" required>
                    <textarea name="message" rows="5" placeholder="Tell Claire if you want this original, a similar size, or a custom version."></textarea>
                    <button type="submit" class="button button-secondary">Send inquiry</button>
                </form>
            @endif
        </div>
    </div>
</section>

@if(count($related))
<section class="section">
    <div class="container">
        <div class="section-heading reveal-up section-title-box">
            <span class="eyebrow">You may also like</span>
            <h2>More pieces from this category.</h2>
        </div>
        <div class="product-grid">
            @foreach(array_slice($related, 0, 3) as $item)
                <a class="product-card reveal-up" href="{{ route('products.show', $item['slug']) }}">
                    <div class="product-card-image" @if(!empty($item['images'][0])) style="background-image:url('{{ str_starts_with($item['images'][0], 'http') ? $item['images'][0] : asset($item['images'][0]) }}')" @endif></div>
                    <div class="product-card-body">
                        <span class="tag">{{ $item['tag'] }}</span>
                        <h3>{{ $item['name'] }}</h3>
                        <div class="product-card-meta">
                            <strong>{{ $item['price'] }}</strong>
                            <span>View piece</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
