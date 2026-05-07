<header class="site-header" id="siteHeader">
    <div class="container">
        <div class="header-inner glass-shell header-shell">
            <a href="{{ route('home') }}" class="logo-link" aria-label="Claire Stefanich Arts home">
                <img src="{{ asset('assets/images/clara-logo.png') }}" alt="Claire Stefanich Arts logo">
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('products.index') }}">Shop</a>
                <a href="{{ route('prints') }}">Prints</a>
                <a href="{{ route('commissions') }}">Commissions</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('about') }}">About Me</a>
                <a href="{{ route('testimonials') }}">Testimonials</a>
            </nav>

            <div class="header-actions">
                <div class="header-socials" aria-label="Social links">
                    <a href="https://www.instagram.com/clairestefanich.art/" class="header-social-link" target="_blank" rel="noreferrer" aria-label="Instagram">
                        <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                            <defs>
                                <linearGradient id="header-instagram-gradient" x1="0" y1="0" x2="16" y2="16" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#ff8762" />
                                    <stop offset="34%" stop-color="#4b8fdc" />
                                    <stop offset="66%" stop-color="#7cc9b5" />
                                    <stop offset="100%" stop-color="#efc24e" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#header-instagram-gradient)" stroke="none" d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@clairestefanich.art" class="header-social-link" target="_blank" rel="noreferrer" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <defs>
                                <linearGradient id="header-tiktok-gradient" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#ff8762" />
                                    <stop offset="34%" stop-color="#4b8fdc" />
                                    <stop offset="66%" stop-color="#7cc9b5" />
                                    <stop offset="100%" stop-color="#efc24e" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#header-tiktok-gradient)" d="M14 4c.5 2.1 1.9 3.6 4 4.2v2.5c-1.4-.1-2.6-.5-3.8-1.2v6c0 2.8-2.3 5-5.1 5S4 18.3 4 15.6c0-2.7 2.2-4.9 4.9-4.9.4 0 .8 0 1.2.1v2.7a3 3 0 0 0-1.1-.2c-1.3 0-2.3 1-2.3 2.3 0 1.4 1 2.4 2.3 2.4 1.5 0 2.4-.9 2.4-2.8V4H14Z"></path>
                        </svg>
                    </a>
                </div>
                <a href="{{ route('cart.index') }}" class="cart-link" aria-label="Shopping cart">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                        <defs>
                            <linearGradient id="header-cart-gradient" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#ff8762" />
                                <stop offset="34%" stop-color="#4b8fdc" />
                                <stop offset="66%" stop-color="#7cc9b5" />
                                <stop offset="100%" stop-color="#efc24e" />
                            </linearGradient>
                        </defs>
                        <circle cx="9" cy="21" r="1" stroke="url(#header-cart-gradient)" stroke-width="2"></circle>
                        <circle cx="20" cy="21" r="1" stroke="url(#header-cart-gradient)" stroke-width="2"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke="url(#header-cart-gradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    @php
                        $cartCount = count(session()->get('cart', [])) + count(session()->get('printful_cart', []));
                    @endphp
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
                <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNavPanel">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <nav class="mobile-nav-panel glass-shell" id="mobileNavPanel" aria-label="Mobile navigation" hidden>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('products.index') }}">Shop</a>
            <a href="{{ route('prints') }}">Prints</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('about') }}">About Me</a>
            <a href="{{ route('testimonials') }}">Testimonials</a>
            <div class="mobile-nav-socials" aria-label="Social links">
                <a href="https://www.instagram.com/clairestefanich.art/" class="header-social-link" target="_blank" rel="noreferrer" aria-label="Instagram">
                    <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                        <defs>
                            <linearGradient id="mobile-instagram-gradient" x1="0" y1="0" x2="16" y2="16" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#ff8762" />
                                <stop offset="34%" stop-color="#4b8fdc" />
                                <stop offset="66%" stop-color="#7cc9b5" />
                                <stop offset="100%" stop-color="#efc24e" />
                            </linearGradient>
                        </defs>
                        <path fill="url(#mobile-instagram-gradient)" stroke="none" d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg>
                </a>
                <a href="https://www.tiktok.com/@clairestefanich.art" class="header-social-link" target="_blank" rel="noreferrer" aria-label="TikTok">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <defs>
                            <linearGradient id="mobile-tiktok-gradient" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#ff8762" />
                                <stop offset="34%" stop-color="#4b8fdc" />
                                <stop offset="66%" stop-color="#7cc9b5" />
                                <stop offset="100%" stop-color="#efc24e" />
                            </linearGradient>
                        </defs>
                        <path fill="url(#mobile-tiktok-gradient)" d="M14 4c.5 2.1 1.9 3.6 4 4.2v2.5c-1.4-.1-2.6-.5-3.8-1.2v6c0 2.8-2.3 5-5.1 5S4 18.3 4 15.6c0-2.7 2.2-4.9 4.9-4.9.4 0 .8 0 1.2.1v2.7a3 3 0 0 0-1.1-.2c-1.3 0-2.3 1-2.3 2.3 0 1.4 1 2.4 2.3 2.4 1.5 0 2.4-.9 2.4-2.8V4H14Z"></path>
                    </svg>
                </a>
            </div>
        </nav>
    </div>
</header>

<style>
    .header-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-socials {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        color: rgba(255, 255, 255, 0.8);
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        transition: color 0.3s ease, transform 0.2s ease;
    }

    .header-social-link:hover {
        color: rgba(255, 255, 255, 1);
        transform: translateY(-1px);
    }

    .header-social-link svg {
        width: 100%;
        height: 100%;
    }

    .cart-link {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        color: rgba(255, 255, 255, 0.8);
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        transition: color 0.3s ease, transform 0.2s ease;
        text-decoration: none;
    }

    .cart-link:hover {
        color: rgba(255, 255, 255, 1);
        transform: translateY(-1px);
    }

    .cart-link svg {
        width: 100%;
        height: 100%;
    }

    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 10px;
        animation: badge-bounce 0.5s ease;
    }

    @keyframes badge-bounce {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }

    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        gap: 5px;
    }

    .mobile-menu-toggle span {
        display: block;
        width: 100%;
        height: 2px;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: flex;
        }

        .header-socials {
            gap: 0.75rem;
        }
    }
</style>
