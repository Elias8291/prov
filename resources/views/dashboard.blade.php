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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script type="module" src="{{ asset('assets/js/sat/pdf-processor.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/sat/sat-scraper.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/sat/utils.js') }}"></script>
     <script type="module" src="{{ asset('assets/js/validaciones.js') }}"></script>
      <script type="module" src="{{ asset('assets/js/navegacion.js') }}"></script>
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        let isMobile = window.innerWidth <= 992;
        const userProfile = document.querySelector('.user-profile');

        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            userProfile.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove('active');
            }
        });

        function checkMobile() {
            isMobile = window.innerWidth <= 992;
            if (!isMobile) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        }

        menuToggle.addEventListener('click', () => {
            if (isMobile) {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            }
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            ODA
        });

        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                if (isMobile) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });

        window.addEventListener('resize', checkMobile);

        checkMobile();
    </script>
    @stack('scripts')
</body>

</html>
