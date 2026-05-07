@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Commissions'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block section-title-box">
        <span class="eyebrow">Commissions</span>
        <h1>Order a custom artwork made from your photo, memory, or idea.</h1>
        <p>Dog portraits, baby pictures, landscapes, travel memories, meaningful places, and custom gift ideas can all start from this page.</p>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container">
        <div class="commission-hero reveal-up">
            <div class="commission-hero-copy">
                <span class="eyebrow">Tailored artwork</span>
                <h2>From your photo to a finished piece.</h2>
                <p>Share references, size ideas, and timing. Claire replies with recommendations and a clear next step for your custom artwork.</p>
                <div class="center-actions">
                    <a class="button" href="#commission-request">Start your request</a>
                </div>
            </div>
        </div>

        <div class="commission-category-grid">
            @forelse($commissionExamples as $product)
                <a class="commission-category-card reveal-up" href="{{ route('products.show', $product['slug']) }}">
                    <div class="commission-collage dogs-collage">
                        @foreach(array_slice($product['images'], 0, 3) as $image)
                            <img src="{{ asset($image) }}" alt="{{ $product['name'] }} example">
                        @endforeach
                    </div>
                    <h3>{{ $product['name'] }}</h3>
                    <p>{{ $product['description'] }}</p>
                </a>
            @empty
                <article class="commission-category-card reveal-up"><h3>Custom portraits</h3><p>Commission examples can be added from the admin panel.</p></article>
            @endforelse
        </div>

        <div class="commission-request-panel reveal-up" id="commission-request">
            <div class="commission-request-copy">
                <span class="eyebrow">Start here</span>
                <h2>Commission request</h2>
                <p>Send Claire the type of artwork you want, your contact info, and a short description. The request will appear in the admin panel.</p>
            </div>
            <form method="post" action="{{ route('commissions.submit') }}" class="commission-form">
                @csrf
                <input type="text" name="name" placeholder="Your name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="text" name="phone" placeholder="Phone or Instagram handle optional">
                <select name="commission_type" required>
                    <option value="">Choose commission type</option>
                    <option value="Dog portrait">Dog portrait</option>
                    <option value="Baby picture">Baby picture</option>
                    <option value="Landscape">Landscape</option>
                    <option value="Custom idea">Custom idea</option>
                </select>
                <input type="text" name="budget" placeholder="Budget optional">
                <input type="text" name="deadline" placeholder="Deadline optional">
                <textarea name="message" rows="6" placeholder="Tell Claire about the photo, size idea, deadline, colors, or story behind the commission." required></textarea>
                <button type="submit" class="button">Send commission request</button>
            </form>
        </div>
    </div>
</section>
@endsection
