@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Testimonials'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Testimonials</span>
        <h1>Kind words from collectors and commission clients.</h1>
        <p>This page shows the testimonials that Claire can add and manage from the admin panel.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="review-grid testimonial-page-grid">
            @forelse($testimonials as $testimonial)
                <article class="review-card testimonial-card reveal-up">
                    @if($testimonial['image'])
                        <img src="{{ asset($testimonial['image']) }}" alt="{{ $testimonial['name'] }} testimonial image">
                    @endif
                    <span class="eyebrow">{{ $testimonial['context'] }}</span>
                    <h3>{{ $testimonial['name'] }}</h3>
                    <p>“{{ $testimonial['body'] }}”</p>
                </article>
            @empty
                <div class="empty-state glass-shell">No testimonials have been added yet.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
