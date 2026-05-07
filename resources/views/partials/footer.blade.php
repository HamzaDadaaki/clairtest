<footer class="site-footer">
    <div class="container">
        <div class="footer-grid glass-shell footer-shell">
            <div>
                <img class="footer-logo" src="{{ asset('assets/images/clara-logo.png') }}" alt="Claire Stefanich Arts logo">
                <p>Original paintings and prints inspired by travel, color, soft memories, and handmade storytelling.</p>
            </div>
            <div>
                <h4>Pages</h4>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}">Shop</a>
                <a href="{{ route('prints') }}">Prints</a>
                <a href="{{ route('commissions') }}">Commissions</a>
                <a href="{{ route('testimonials') }}">Testimonials</a>
                <a href="{{ route('about') }}">About Me</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
            <div>
                <h4>Reach Out</h4>
                <a href="mailto:clairestefanichart@gmail.com">clairestefanichart@gmail.com</a>
                <a href="https://www.instagram.com/clairestefanich.art/" target="_blank" rel="noreferrer">Instagram</a>
                <a href="{{ route('admin.login') }}">Studio Admin</a>
            </div>
        </div>
    </div>
</footer>
