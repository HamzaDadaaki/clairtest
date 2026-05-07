@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Home'])

@section('content')
<section class="hero-section">
    <div class="container">
        <div class="hero-shell glass-shell reveal-up">
            <div class="hero-slider">
                <article class="hero-slide active" style="background-image: url('{{ asset('assets/images/claire-gallery/cafeteria-cafe-latte.jpg') }}')">
                    <div class="hero-content container">
                        <span class="eyebrow">Originals · Prints · Commissions</span>
                        <h1>Claire Stefanich Arts</h1>
                        <p>Colorful handmade artwork, original pieces, fine art prints, and custom commissions presented clearly in one elegant shop.</p>
                        <div class="hero-button-row">
                            <a class="button hero-primary-button" href="{{ route('products.index') }}">Shop arts</a>
                            <a class="button button-secondary" href="{{ route('commissions') }}">Order a commission</a>
                        </div>
                    </div>
                </article>
                <article class="hero-slide" style="background-image: url('{{ asset('assets/images/claire-gallery/dog-portrait-sienna-ridd.jpg') }}')">
                    <div class="hero-content container">
                        <span class="eyebrow">Custom commissions</span>
                        <h2>Personal portraits, landscapes, pet paintings, and meaningful gifts.</h2>
                        <p>Commission requests now have a dedicated page so visitors know exactly where to start.</p>
                        <a class="button hero-primary-button" href="{{ route('commissions') }}">View commissions</a>
                    </div>
                </article>
                <article class="hero-slide" style="background-image: url('{{ asset('assets/images/claire-gallery/aui-mosque.jpg') }}')">
                    <div class="hero-content container">
                        <span class="eyebrow">Fine art prints</span>
                        <h2>Curated prints, stickers, bags, and gift items.</h2>
                        <p>The prints section is separated from originals so visitors can browse quickly.</p>
                        <a class="button hero-primary-button" href="{{ route('prints') }}">Browse prints</a>
                    </div>
                </article>
            </div>
            <div class="slider-dots">
                <button class="dot active" data-slide="0" aria-label="Slide 1"></button>
                <button class="dot" data-slide="1" aria-label="Slide 2"></button>
                <button class="dot" data-slide="2" aria-label="Slide 3"></button>
            </div>
        </div>
    </div>
</section>

<section class="section claire-path-section">
    <div class="container">
        <div class="section-heading reveal-up section-title-box">
            <span class="eyebrow">Choose your path</span>
            <h2>What would you like to explore?</h2>
            <p>Visitors can quickly choose between originals, prints, and custom commissions.</p>
        </div>
        <div class="path-grid">
            <a class="path-card reveal-up" href="{{ route('products.index', ['category' => 'original']) }}">
                <span>01</span>
                <h3>Originals</h3>
                <p>One-of-a-kind handmade pieces available to buy or already collected.</p>
            </a>
            <a class="path-card reveal-up" href="{{ route('prints') }}">
                <span>02</span>
                <h3>Prints</h3>
                <p>High-quality print products available in different formats.</p>
            </a>
            <a class="path-card reveal-up" href="{{ route('commissions') }}">
                <span>03</span>
                <h3>Commissions</h3>
                <p>Custom pet portraits, baby pictures, landscapes, and personal artwork requests.</p>
            </a>
        </div>
    </div>
</section>

<section class="section section-tight-top" id="originals">
    <div class="container">
        <div class="section-heading reveal-up section-title-box">
            <span class="eyebrow">Latest originals</span>
            <h2>Available original artworks.</h2>
            <p>Only original artworks for sale appear here. Sold pieces stay separated.</p>
        </div>
        <div class="product-grid wide-grid originals-grid">
            @forelse(array_slice($latestOriginals, 0, 4) as $product)
                <a class="product-card reveal-up original-card" href="{{ route('products.show', $product['slug']) }}">
                    <div class="product-card-image" @if(!empty($product['images'][0])) style="background-image:url('{{ str_starts_with($product['images'][0], 'http') ? $product['images'][0] : asset($product['images'][0]) }}')" @endif>
                        <span class="art-status-badge for-sale">For sale</span>
                    </div>
                    <div class="product-card-body">
                        <span class="tag">{{ $product['tag'] }}</span>
                        <h3>{{ $product['name'] }}</h3>
                        <p>{{ $product['description'] }}</p>
                        <div class="product-card-meta">
                            <strong>{{ $product['price'] }}</strong>
                            <span>View piece</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state glass-shell">No originals are available yet.</div>
            @endforelse
        </div>
        <div class="center-actions reveal-up">
            <a class="button button-secondary" href="{{ route('products.index', ['category' => 'original']) }}">View all originals</a>
        </div>
    </div>
</section>

<section class="section print-on-demand-section" id="prints">
    <div class="container">
        <div class="section-heading reveal-up section-title-box">
            <span class="eyebrow">Prints</span>
            <h2>Print collections available to order.</h2>
            <p>Print products are separated from original art so visitors can browse both clearly.</p>
        </div>
        <div class="pod-grid">
            @forelse($printfulProducts as $product)
                <article class="pod-card reveal-up">
                    @if(!empty($product['images'][0]))
                        <div class="pod-card-image" style="background-image: url('{{ str_starts_with($product['images'][0], 'http') ? $product['images'][0] : asset($product['images'][0]) }}')"></div>
                    @endif
                    <div class="pod-card-content">
                        <span class="pod-card-tag">Print</span>
                        <h3>{{ $product['name'] }}</h3>
                        <p class="pod-card-price">{{ $product['price'] }}</p>
                        <div class="pod-card-actions">
                            <a class="button button-sm" href="{{ route('products.show', $product['slug']) }}">View piece</a>
                            <form method="post" action="{{ route('cart.add_printful', $product['printful_id']) }}" style="width: 100%;">
                                @csrf
                                <button class="button button-sm" type="submit" style="width: 100%;">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Art prints</h3><p>High-quality reproductions of selected Claire Stefanich artworks.</p></article>
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Stickers</h3><p>Colorful small products for fans, gifts, and everyday use.</p></article>
                <article class="pod-card reveal-up"><span>Coming soon</span><h3>Bags</h3><p>Artwork on tote bags and useful products through Printful.</p></article>
            @endforelse
        </div>
        <div class="center-actions reveal-up">
            <a class="button button-secondary" href="{{ route('prints') }}">View all prints</a>
        </div>
    </div>
</section>

<section class="section commissions-section" id="commissions">
    <div class="container">
        <div class="commission-hero reveal-up">
            <div class="commission-hero-copy">
                <span class="eyebrow">Commissions</span>
                <h2>Order a custom artwork made specifically for you.</h2>
                <p>Claire creates custom dog portraits, baby pictures, landscapes, places, and personal ideas from your favorite photos. The full request form is on the commissions page.</p>
                <div class="center-actions">
                    <a class="button" href="{{ route('commissions') }}">Open commissions page</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section testimonials-section" id="testimonials">
    <div class="container">
        @if($featuredTestimonial)
            <div class="testimonial-feature reveal-up">
                <div class="testimonial-feature-image">
                    <img src="{{ asset($featuredTestimonial['image']) }}" alt="{{ $featuredTestimonial['name'] }} testimonial image">
                </div>
                <div class="testimonial-feature-copy">
                    <span class="eyebrow">Kind words</span>
                    <h2>{{ $featuredTestimonial['context'] }}</h2>
                    <blockquote>“{{ $featuredTestimonial['body'] }}”</blockquote>
                    <p><strong>— {{ $featuredTestimonial['name'] }}</strong></p>
                    <a class="button button-secondary" href="{{ route('testimonials') }}">View all testimonials</a>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="section email-list-section">
    <div class="container">
        <div class="email-list-card reveal-up">
            <div class="email-list-content">
                <span class="eyebrow">Stay in the loop</span>
                <h2>Join my email list</h2>
                <p>Get updates on new originals, commission openings, prints, and colorful little surprises.</p>
                <form class="email-form" method="post" action="{{ route('subscribe') }}">
                    @csrf
                    <div class="email-input-group">
                        <input type="text" name="name" placeholder="Name" class="email-input" required aria-label="Name">
                        <input type="email" name="email" placeholder="Email" class="email-input" required aria-label="Email address">
                        <button type="submit" class="button button-email-submit">Join the List</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
window.heroProductImages = @json(array_map(fn ($image) => str_starts_with($image, 'http') ? $image : asset($image), $heroProductImages));
</script>
@endsection
