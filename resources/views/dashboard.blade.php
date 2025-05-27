<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Gobierno')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body>
    @include('layouts.header')
    <div class="sidebar-mobile-overlay" id="sidebar-overlay"></div>
    @include('layouts.sidebar')
    <main class="content">
        @yield('content')
    </main>
    @include('layouts.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script type="module" src="{{ asset('assets/js/sat/pdf-processor.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/sat/sat-scraper.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/sat/utils.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/validaciones.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/sidebar.js') }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>