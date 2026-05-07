@extends('layouts.app', ['title' => 'Afayar Software Development'])

@section('content')
<section class="hero-banner">
    <img src="{{ asset('assets/images/hero-bg.webp') }}" alt="Afayar hero visual" class="hero-banner-image">
    <div class="hero-banner-overlay"></div>
    <div class="container hero-banner-content">
        <div class="hero-banner-copy reveal-on-scroll">
            <span class="eyebrow">Software. Service. Growth.</span>
            <h1>Technology that makes your business look stronger, work smarter, and sell faster.</h1>
            <p>
                Afayar Software Development creates premium software and digital services for businesses that want more than something cheap and ordinary. We build practical tools, elegant websites, and strong digital systems that help you win trust, save time, and grow with confidence.
            </p>
            <div class="hero-actions">
                <a href="{{ route('products.index') }}" class="primary-cta large">Explore our products</a>
                <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '212640611520') }}?text=Hello%20Afayar%2C%20I%20want%20to%20talk%20about%20a%20project." class="ghost-button large" target="_blank" rel="noopener">Contact commercial</a>
            </div>
            <div class="hero-metrics hero-banner-metrics">
                <div>
                    <strong>180+</strong>
                    <span>Businesses reached</span>
                </div>
                <div>
                    <strong>Premium</strong>
                    <span>Technology-first identity</span>
                </div>
                <div>
                    <strong>Fast</strong>
                    <span>Direct WhatsApp contact</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-block first-section">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">Best picks</span>
            <h2>Products and services built to help serious businesses move forward.</h2>
            <p>From software to deployment help, Afayar focuses on solutions with business value, visual quality, and long-term usefulness.</p>
        </div>

        <div class="product-grid three">
            @foreach ($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<section class="section-block collaboration-strip-section">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">Collaborations</span>
            <h2>Let’s build strong ideas together.</h2>
            <p>We are open to creative and business collaboration. For now, we are showcasing the brands closest to this vision.</p>
        </div>

        @include('partials.partners-marquee', ['partners' => $partners])

        <div class="collaboration-cta reveal-on-scroll">
            <p>Have an idea in mind, a brand to launch, or a digital partnership to build?</p>
            <div class="hero-actions center-mobile">
                <a href="{{ route('collaboration') }}" class="ghost-button large">Explore collaboration</a>
                <a href="{{ route('contact') }}" class="primary-cta large">Let’s collaborate</a>
            </div>
        </div>
    </div>
</section>

<section class="section-block stats-section">
    <div class="container">
        <div class="section-heading centered reveal-on-scroll">
            <span class="eyebrow">Trusted by many</span>
            <h2>Businesses choose Afayar because they want something modern, useful, and built with vision.</h2>
        </div>

        <div class="stats-grid">
            @foreach ($stats as $stat)
                <div class="stat-card reveal-on-scroll">
                    <strong data-counter="{{ preg_replace('/[^0-9]/', '', $stat['value']) ?: 0 }}">{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-block cta-panel-wrap">
    <div class="container">
        <div class="cta-panel reveal-on-scroll">
            <div>
                <span class="eyebrow">Contact us</span>
                <h2>Need a software product, website, or custom digital service?</h2>
                <p>Speak directly with Afayar commercial and get a faster path to your next project.</p>
            </div>
            <div class="cta-actions">
                <a href="{{ route('contact') }}" class="ghost-button large">Open contact page</a>
                <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '212640611520') }}?text=Hello%20Afayar%2C%20I%20want%20to%20speak%20with%20commercial." class="primary-cta large" target="_blank" rel="noopener">WhatsApp commercial</a>
            </div>
        </div>
    </div>
</section>
@endsection
