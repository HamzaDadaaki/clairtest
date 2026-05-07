@extends('layouts.app', ['title' => 'Search | Afayar'])

@section('content')
<section class="page-hero compact">
    <div class="container reveal-on-scroll narrow-copy">
        <span class="eyebrow">Search</span>
        <h1>Results for “{{ $query ?: 'all products' }}”</h1>
        <p>{{ count($results) }} result{{ count($results) === 1 ? '' : 's' }} found.</p>
    </div>
</section>

<section class="section-block">
    <div class="container">
        @if (count($results))
            <div class="product-grid three">
                @foreach ($results as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="empty-state glass-card reveal-on-scroll">
                <h2>No result found.</h2>
                <p>Try another keyword like POS, website, support, booking, or inventory.</p>
                <div class="hero-actions center-mobile">
                    <a href="{{ route('products.index') }}" class="primary-cta">Explore products</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
