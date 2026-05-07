@extends('layouts.app', ['title' => 'Collaboration | Afayar'])

@section('content')
<section class="page-hero compact collaboration-hero">
    <div class="container reveal-on-scroll narrow-copy">
        <span class="eyebrow">Collaboration</span>
        <h1>Partnerships, launches, and creative collaboration with Afayar.</h1>
        <p>We collaborate with brands, founders, and business teams that want something modern, premium, and well executed. If you have a strong idea, we can help shape it and launch it with clarity.</p>
        <div class="hero-actions">
            <a href="{{ route('contact') }}" class="primary-cta large">Get in touch</a>
            <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '212640611520') }}?text=Hello%20Afayar%2C%20I%20want%20to%20talk%20about%20a%20collaboration." class="ghost-button large" target="_blank" rel="noopener">WhatsApp direct</a>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="info-strip-grid">
            <div class="glass-card info-mini-card reveal-on-scroll">
                <strong>Fast response</strong>
                <p>We review collaboration requests quickly and clearly.</p>
            </div>
            <div class="glass-card info-mini-card reveal-on-scroll">
                <strong>Premium direction</strong>
                <p>We care about clean presentation, strong identity, and useful execution.</p>
            </div>
            <div class="glass-card info-mini-card reveal-on-scroll">
                <strong>Long-term vision</strong>
                <p>We prefer collaborations that can grow into something serious.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-block soft-section">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">Ways to collaborate</span>
            <h2>Choose the format that fits your idea.</h2>
        </div>

        <div class="product-grid four collaboration-grid">
            <div class="glass-card reveal-on-scroll collaboration-card">
                <span class="pill">Creators</span>
                <h3>Content & campaigns</h3>
                <p>Visual storytelling, launch support, and product presentation that feels premium.</p>
            </div>
            <div class="glass-card reveal-on-scroll collaboration-card">
                <span class="pill">Businesses</span>
                <h3>Retail partnerships</h3>
                <p>Software partnerships, deployment support, and shared commercial opportunities.</p>
            </div>
            <div class="glass-card reveal-on-scroll collaboration-card">
                <span class="pill">Brands</span>
                <h3>Launch support</h3>
                <p>Need a website, digital system, or presentation around a new product or brand? We can build it.</p>
            </div>
            <div class="glass-card reveal-on-scroll collaboration-card">
                <span class="pill">Events</span>
                <h3>Pop-ups & activations</h3>
                <p>For launches or commercial events, we can support the digital side and the visual structure.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">How it works</span>
            <h2>A simple collaboration flow.</h2>
        </div>

        <div class="process-stack">
            <div class="glass-card process-card reveal-on-scroll"><span>01</span><div><h3>Send your idea</h3><p>Tell us what you want to build, launch, or improve.</p></div></div>
            <div class="glass-card process-card reveal-on-scroll"><span>02</span><div><h3>Quick review</h3><p>We review the fit, the direction, and the best next step for both sides.</p></div></div>
            <div class="glass-card process-card reveal-on-scroll"><span>03</span><div><h3>Build together</h3><p>We align design, communication, and execution around a clean result.</p></div></div>
        </div>
    </div>
</section>

<section class="section-block collaboration-strip-section">
    <div class="container">
        <div class="section-heading reveal-on-scroll">
            <span class="eyebrow">Current partner logos</span>
            <h2>Brands in this ecosystem right now.</h2>
        </div>

        @include('partials.partners-marquee', ['partners' => $partners])

        <div class="collaboration-cta reveal-on-scroll">
            <p>Have any idea in mind? Let’s collaborate and build something that looks strong and works well.</p>
            <a href="{{ route('contact') }}" class="primary-cta large">Contact Afayar</a>
        </div>
    </div>
</section>
@endsection
