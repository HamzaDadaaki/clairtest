@extends('layouts.app', ['title' => 'Claire Stefanich Arts | About'])

@section('content')
<section class="page-hero page-hero-compact about-hero">
    <div class="container about-intro reveal-up">
        <div class="about-copy-card">
            <span class="eyebrow">About the artist</span>
            <h1>Claire’s work blends travel, handmade storytelling, and everyday beauty.</h1>
            <p>Claire shares original paintings and prints shaped by study-abroad life in Morocco, travel, and personal observation. The brand identity in this demo is built around that warm mix of handmade art, storytelling, and everyday beauty.</p>
            <div class="about-links">
                <a class="button" href="{{ $instagramUrl }}" target="_blank" rel="noreferrer">Instagram</a>
                <a class="button button-secondary" href="mailto:{{ $email }}">Email Claire</a>
            </div>
        </div>
        <div class="about-portrait-composition">
            <div class="portrait-wrap portrait-main">
                <img src="{{ asset('assets/images/claire.png') }}" alt="Claire portrait">
            </div>
        </div>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container bio-panel about-story-panel reveal-up">
        <div>
            <h2>Studio story</h2>
            <p>The creative direction here is centered on original watercolor paintings, prints, travel-inspired scenes, and custom commissions. The goal is to make the website feel personal, artistic, and memorable rather than generic.</p>
            <p>This demo keeps that same feeling: soft gallery layouts, artistic textures, colorful accents, and a presentation style that makes the work feel premium while staying warm and handmade.</p>
        </div>
        <div class="stacked-art about-gallery-grid">
            <img src="{{ asset('assets/images/moroccan-door.jpg') }}" alt="Moroccan Door watercolor">
            <img src="{{ asset('assets/images/aui-campus.jpg') }}" alt="AUI Campus watercolor">
            <img src="{{ asset('assets/images/iris.jpg') }}" alt="Iris watercolor">
        </div>
    </div>
</section>
@endsection
