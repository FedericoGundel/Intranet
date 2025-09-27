<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>DevStudio - Plataforma de Gestión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @stack('styles')
    <!-- App css -->
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('libs/animate.css/animate.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.8.0/dist/slimselect.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <style>
        :root {
            --bs-topbar-height: 75px;
            --bs-topbar-bg: #f9fbfd;
            --bs-topbar-b-border-color: #e3ebf6;
            --bs-topbar-nav-icon-bg: #fff;
            --bs-topbar-nav-icon-color: #424d65;
            --bs-startbar-width: 270px;
            --bs-startbar-collapsed-width: 70px;
            --bs-startbar-bg: #fff;
            --bs-startbar-e-border-color: #e3ebf6;
            --bs-menu-icon-color: #96a0b5;
            --bs-menu-link-color: #061237;
            --bs-menu-link-active-bg-color: #f9fbfd;
            --bs-nav-link-disc-color: #8997bd;
            --bs-menu-label-color: #a0a5b3;
            --bs-label-color: #656d9a;
            --bs-text-muted: var(--bs-secondary-color);
            --bs-box-shadow-sm: 0 2px 2px rgba(61, 71, 81, 0.05);
            --bs-card-bg: var(--bs-secondary-bg);
            --bs-theme-white-color: #ffffff;
            --bs-border-secondary: #95a0c5;
            --bs-table-head-bg: #f4f6f9;
            --bs-color-primary: {{ $configurations['color_principal'] }};
            --bs-btn-bg: {{ $configurations['color_principal'] }};
        }

        html[data-bs-theme=dark] {
            --bs-light: #303231;
            --bs-light-rgb: 48, 50, 49;
            --bs-dark: #f4f6f9;
            --bs-dark-rgb: 244, 246, 249;
            --bs-label-color: #9797a5;
            --bs-theme-white-color: #2e2e31;
            --bs-body-color: #d9e1ec;
            --bs-text-muted: #aab0b9;
            --bs-box-shadow-sm: 0 2px 2px rgba(61, 71, 81, 0.05);
            --bs-card-bg: #151821;
            --bs-topbar-bg: #141824;
            --bs-topbar-b-border-color: #333547;
            --bs-topbar-nav-icon-bg: #222735;
            --bs-topbar-nav-icon-color: #a1a8bd;
            --bs-secondary: #c3c3c3;
            --bs-secondary-rgb: 195, 195, 195;
            --bs-border-secondary: #333645;
            --bs-table-head-bg: #222735;
            --bs-startbar-bg: {{ $configurations['color_fondo_menu'] }};
            --bs-startbar-e-border-color: #333547;
            --bs-menu-icon-color: #5e748b;
            --bs-menu-link-color: {{ $configurations['color_enlaces_menu'] }};
            --bs-menu-link-active-bg-color: rgba(249, 251, 253, 0.04);
            --bs-menu-link-hover-color: {{ $configurations['color_enlaces_menu_hover'] }};
            --bs-nav-link-disc-color: #8997bd;
            --bs-menu-label-color: #a0a5b3;
        }

        html[data-startbar=dark] {
            --bs-startbar-bg: {{ $configurations['color_fondo_menu'] }};
            --bs-startbar-e-border-color: #333547;
            --bs-menu-icon-color: #5e748b;
            --bs-menu-link-color: {{ $configurations['color_enlaces_menu'] }};
            --bs-menu-link-active-bg-color: rgba(249, 251, 253, 0.04);
            --bs-menu-link-hover-color: {{ $configurations['color_enlaces_menu_hover'] }};
            --bs-nav-link-disc-color: #8997bd;
            --bs-menu-label-color: #a0a5b3;
            --bs-dark: #f4f6f9;
            --bs-box-shadow-sm: 0 2px 2px rgba(61, 71, 81, 0.05);
        }
    </style>
</head>

<style>
    .preloder {
        height: 100vh;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9fbfd;
        z-index: 999999;

    }


    .loder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 5px solid #D1DFFF;
        border-top: 8px solid #3775FF;
        animation: spinner 1s linear infinite;
    }

    @keyframes spinner {
        0% {
            transform: rotate(0deg);

        }

        50% {
            border-top-width: 5px;
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @media screen and (max-width: 668px) {
        .loder {
            height: 60px;
            width: 60px;
            border-top: 6px solid #3775FF;
        }
    }
</style>

<div class="preloder">
    <div class="loder">

    </div>
</div>

<body>

    @if (!in_array(Route::currentRouteName(), ['login', 'register', 'password.request', 'password.reset']))
        @include('partials.header')
    @endif

    <main>
        @yield('content')
    </main>



    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.lordicon.com/ritcuqlt.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.10.2/lottie.min.js"></script>

    <script>
        window.addEventListener("load", function() {
            // Busca el preloader y le aplica una transición
            const preloader = document.querySelector(".preloder");
            preloader.style.opacity = "0";
            /* lottie.loadAnimation({
                 container: document.getElementById('iconscoutLottie'),
                 renderer: 'svg',
                 loop: true,
                 autoplay: true,
                 path: "{{ asset('/images/lot/c.json') }}" // tu archivo descargado
             });*/
            // Después de la animación, lo saca del DOM
            setTimeout(() => {
                preloader.style.display = "none";
                var icons = document.querySelectorAll("lord-icon");
                icons.forEach(icon => {
                    icon.setAttribute("colors",
                        "primary:{{ $configurations['color_principal'] }},secondary:{{ $configurations['color_secundario'] }}"
                    );
                });
            }, 500); // 500ms = el tiempo de la transición
        });
    </script>



    @stack('scripts')


</body>

<!--end body-->

</html>
