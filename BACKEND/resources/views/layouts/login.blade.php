<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#288479">

    <title>{{ config('app.name', 'Dream Travel and Tours') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/dream-logo.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/hyper-config.js') }}"></script>

    <!-- App css -->
    <link href="{{ asset('assets/css/app-saas.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Scripts -->
    
    <link href="{{ asset('assets/css/brand.css') }}" rel="stylesheet" type="text/css" />
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .toast-success {
        background-color: #51a351 !important;
    }

    .toast-error {
        background-color: #bd362f !important;
    }
</style>

<body class="authentication-bg pb-0">

    <!-- Pre-loader -->
    <div id="preloader">
        <div id="status">
            <div class="bouncing-loader">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <!-- End Preloader-->

    <div id="app">
        <main class="">
            @yield('content')
        </main>
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <!--  Toastr Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    @yield('script')
    @include('scripts.authentication')
    <script>
        document.addEventListener('click', function(event) {
            const toggle = event.target.closest('[data-password-toggle]');
            if (!toggle) {
                return;
            }

            const input = toggle.closest('.input-group')?.querySelector('input') ||
                document.getElementById(toggle.getAttribute('data-password-toggle'));
            const icon = toggle.querySelector('i');
            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            icon.classList.toggle('mdi-eye-outline', !isHidden);
            icon.classList.toggle('mdi-eye-off-outline', isHidden);
        });
    </script>
</body>

</html>
