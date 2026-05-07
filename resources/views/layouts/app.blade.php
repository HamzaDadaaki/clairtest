<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Claire Stefanich Arts' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/clara.css') }}">
</head>
<body class="{{ $bodyClass ?? 'site-body has-video-background' }}">
    <div class="site-video-shell" aria-hidden="true">
        <div
            class="site-background-image"
            style="background-image: url('{{ asset('assets/images/background.webp') }}');"
        ></div>
        <div class="site-video-tint"></div>
        <div class="site-video-glow"></div>
    </div>

    <div class="site-page-shell">
        @include('partials.header')

        @if (session('success'))
            <div class="flash-message">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-message flash-error">{{ session('error') }}</div>
        @endif

        <main>
            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    <script src="{{ asset('assets/js/clara.js') }}"></script>
</body>
</html>
