<article class="product-card reveal-on-scroll">
    <a href="{{ route('products.show', $product['slug']) }}" class="product-visual" aria-label="{{ $product['name'] }}">
        <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }} visual">
    </a>

    <div class="product-card-content">
        <div class="product-meta">
            <span class="pill">{{ $product['type'] }}</span>
            <span class="muted-tag">{{ $product['badge'] }}</span>
        </div>
        <h3>{{ $product['name'] }}</h3>
        <p>{{ $product['short'] }}</p>
        <div class="price-line">{{ $product['price_label'] }}</div>
        <div class="product-actions">
            <a href="{{ route('products.show', $product['slug']) }}" class="ghost-button">View details</a>
            <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '212640611520') }}?text={{ rawurlencode('Hello Afayar, I want more information about '.$product['name'].'.') }}" class="mini-button" target="_blank" rel="noopener">Contact us</a>
        </div>
    </div>
</article>
