@extends('layouts.app', ['title' => $product['name'].' | Afayar'])

@section('content')
<section class="product-hero single-product-hero">
    <div class="container product-hero-grid single-product-balance">
        <div class="reveal-on-scroll product-copy-side">
            <span class="eyebrow">{{ $product['type'] }}</span>
            <h1>{{ $product['name'] }}</h1>
            <p class="lead">{{ $product['description'] }}</p>
            <div class="meta-line">
                <span class="pill">{{ $product['badge'] }}</span>
                <span class="price-line">{{ $product['price_label'] }}</span>
            </div>
            <div class="hero-actions product-actions-spaced">
                <a href="{{ $whatsAppUrl }}" class="primary-cta large" target="_blank" rel="noopener">Contact us on WhatsApp</a>
                <a href="{{ route('products.index') }}" class="ghost-button large">Back to products</a>
            </div>
        </div>

        <div class="glass-card reveal-on-scroll product-gallery-card" data-product-gallery>
            @php($gallery = $product['gallery'] ?? [$product['image']])
            <div class="product-gallery-stage">
                @foreach ($gallery as $index => $image)
                    <button
                        type="button"
                        class="gallery-slide @if($index === 0) is-active @endif"
                        data-gallery-slide
                        data-gallery-index="{{ $index }}"
                        data-gallery-full="{{ asset($image) }}"
                        class="gallery-slide {{ $index === 0 ? 'is-active' : '' }}"
                        aria-label="Open {{ $product['name'] }} image {{ $index + 1 }}"
                    >
                        <img src="{{ asset($image) }}" alt="{{ $product['name'] }} image {{ $index + 1 }}">
                    </button>
                @endforeach

                @if(count($gallery) > 1)
                    <button type="button" class="gallery-nav prev" data-gallery-prev aria-label="Previous image">&#10094;</button>
                    <button type="button" class="gallery-nav next" data-gallery-next aria-label="Next image">&#10095;</button>
                @endif
            </div>

            @if(count($gallery) > 1)
                <div class="gallery-thumbs" role="tablist" aria-label="Product images">
                    @foreach ($gallery as $index => $image)
                        <button
                            type="button"
                            class="gallery-thumb @if($index === 0) is-active @endif"
                            data-gallery-thumb
                            data-gallery-index="{{ $index }}"
                            aria-label="Show image {{ $index + 1 }}"
                        >
                            <img src="{{ asset($image) }}" alt="{{ $product['name'] }} thumbnail {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="product-gallery-tip">
                <span>Use arrows to switch images and click the image to zoom.</span>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container two-col-grid">
        <div class="glass-card reveal-on-scroll">
            <h2>Description</h2>
            <p>{{ $product['description'] }}</p>
            <p>{{ $product['short'] }}</p>
            <div class="product-copy-card">
                <span class="eyebrow">Why it matters</span>
                <h3>{{ $product['hero_note'] }}</h3>
                <ul class="feature-list">
                    @foreach ($product['features'] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="glass-card reveal-on-scroll">
            <h2>Reviews</h2>
            <div class="review-list">
                @foreach ($product['review'] as $review)
                    <div class="review-card">
                        <p>“{{ $review['text'] }}”</p>
                        <strong>{{ $review['name'] }}</strong>
                        <span>{{ $review['role'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section-block soft-section">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">You may also like</span>
            <h2>Other Afayar solutions</h2>
        </div>
        <div class="product-grid three">
            @foreach ($relatedProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<div class="image-lightbox" data-image-lightbox hidden>
    <button type="button" class="image-lightbox-close" data-lightbox-close aria-label="Close image preview">&times;</button>
    <button type="button" class="image-lightbox-nav prev" data-lightbox-prev aria-label="Previous image">&#10094;</button>
    <figure class="image-lightbox-figure">
        <img src="" alt="" data-lightbox-image>
    </figure>
    <button type="button" class="image-lightbox-nav next" data-lightbox-next aria-label="Next image">&#10095;</button>
</div>
@endsection
