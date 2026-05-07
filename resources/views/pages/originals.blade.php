@extends('layouts.app', ['title' => 'Originals For Sale | Claire Stefanich Arts'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block">
        <span class="eyebrow">Originals</span>
        <h1>Originals For Sale</h1>
        <p>Original watercolor artworks available for collectors. Originals, commissions, and prints are separate offerings.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container product-grid wide-grid">
        @foreach($forSale as $piece)
            <div class="product-card reveal-up">
                <div class="product-card-image" style="background-image:url('{{ asset($piece['image']) }}')"></div>
                <div class="product-card-body">
                    <span class="tag">Original for sale</span>
                    <h3>{{ $piece['name'] }}</h3>
                    <p>Price on request</p>
                    <div class="product-card-meta">
                        <strong>Price on request</strong>
                        <a href="{{ route('contact') }}" class="button">Ask to buy</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="container reveal-up" style="margin-top:2.5rem">
        <div class="section-heading">
            <span class="eyebrow">Collected Pieces</span>
            <h2>Sold Originals</h2>
        </div>

        <div class="product-grid">
            @foreach($sold as $piece)
                <div class="product-card sold reveal-up">
                    <div class="product-card-image" style="background-image:url('{{ asset($piece['image']) }}')"></div>
                    <div class="product-card-body">
                        <span class="tag">Sold</span>
                        <h3>{{ $piece['name'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
