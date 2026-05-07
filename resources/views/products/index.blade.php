@extends('layouts.app', ['title' => 'Afayar Products'])

@section('content')
<section class="page-hero compact products-page-hero">
    <div class="container reveal-on-scroll">
        <span class="eyebrow">Products and services</span>
        <h1>Browse the Afayar catalog.</h1>
        <p>Explore software products, deployment support, and custom digital services designed to give businesses more control and a stronger image.</p>
    </div>
</section>

<section class="section-block products-list-section">
    <div class="container">
        <div class="filter-row product-filter-row reveal-on-scroll">
            <a href="{{ route('products.index') }}" @class(['filter-chip', 'active' => $filter === ''])>All</a>
            <a href="{{ route('products.index', ['type' => 'Software']) }}" @class(['filter-chip', 'active' => $filter === 'Software'])>Software</a>
            <a href="{{ route('products.index', ['type' => 'Service']) }}" @class(['filter-chip', 'active' => $filter === 'Service'])>Services</a>
        </div>

        <div class="product-grid three">
            @foreach ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endsection
