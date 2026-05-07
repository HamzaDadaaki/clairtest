@extends('layouts.app', ['title' => 'Claire Stefanich Arts | Contact'])

@section('content')
<section class="page-hero page-hero-compact">
    <div class="container reveal-up page-title-block">
        <span class="eyebrow">Contact</span>
        <h1>Let’s talk about a piece, a commission, or a question.</h1>
    </div>
</section>

<section class="section section-tight-top">
    <div class="container contact-grid">
        <div class="contact-card reveal-up">
            <h2>Studio details</h2>
            <p><strong>Email</strong><br>clairestefanichart@gmail.com</p>
            <p><strong>Instagram</strong><br><a href="https://www.instagram.com/clairestefanich.art/" target="_blank" rel="noreferrer">@clairestefanich.art</a></p>
            <p><strong>Commissions</strong><br>Custom watercolor requests and direct purchase inquiries are welcome.</p>
        </div>

        <form method="post" action="{{ route('contact.submit') }}" class="contact-form reveal-up">
            @csrf
            <input type="text" name="name" placeholder="Your name" required>
            <input type="text" name="phone" placeholder="Phone number">
            <input type="email" name="email" placeholder="Email address" required>
            <textarea name="message" rows="7" placeholder="Tell Claire what you are looking for..." required></textarea>
            <button type="submit" class="button">Send Message</button>
        </form>
    </div>
</section>
@endsection
