@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Shop'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Shop</span>
        <h1>Originals and sold pieces.</h1>
        <p>The shop shows only original artworks and sold original pieces. Prints and commissions stay separate.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="shop-filter-bar reveal-up">
            @foreach($categoryLabels as $key => $label)
                <a class="shop-filter-link {{ $category === $key ? 'active' : '' }}" href="{{ route('products.index', $key === 'all' ? [] : ['category' => $key]) }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="product-grid wide-grid originals-grid">
            @forelse($products as $product)
                <a class="product-card reveal-up original-card" href="{{ route('products.show', $product['slug']) }}">
                    <div class="product-card-image" @if(!empty($product['images'][0])) style="background-image:url('{{ str_starts_with($product['images'][0], 'http') ? $product['images'][0] : asset($product['images'][0]) }}')" @endif>
                        <span class="art-status-badge {{ $product['status'] === 'sold' ? 'sold' : 'for-sale' }}">
                            {{ $product['status'] === 'sold' ? 'Sold' : ucfirst(str_replace('_', ' ', $product['status'])) }}
                        </span>
                    </div>
                    <div class="product-card-body">
                        <span class="tag">{{ $product['tag'] }}</span>
                        <h3>{{ $product['name'] }}</h3>
                        <p>{{ $product['description'] }}</p>
                        <div class="product-card-meta">
                            <strong>{{ $product['price'] }}</strong>
                            <span>{{ $product['button_label'] ?: 'View' }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state glass-shell">No products found in this category yet.</div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="shop-pagination reveal-up">
                @if($products->onFirstPage())
                    <span class="pagination-disabled">Previous</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}">Previous</a>
                @endif

                @foreach(range(1, $products->lastPage()) as $page)
                    @if($page === $products->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $products->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}">Next</a>
                @else
                    <span class="pagination-disabled">Next</span>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection
