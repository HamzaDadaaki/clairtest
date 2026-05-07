<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Claire Stefanich Arts | Admin' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/clara.css') }}">
</head>
<body class="admin-body">
    @if (session('success'))
        <div class="flash-message admin-flash">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash-message admin-flash flash-error">{{ session('error') }}</div>
    @endif

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('assets/js/clara.js') }}"></script>
</body>
</html>
