<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tab Icon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo mini.png') }}" type="image/x-icon">

    <!-- Fonts -->
    {{-- <link href="https://fonts.cdnfonts.com/css/lexend-deca" rel="stylesheet"> --}}
    {{-- <link href="https://fonts.cdnfonts.com/css/poppins" rel="stylesheet"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Alert -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Flowbite -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" />

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Marker Cluster -->
    <link rel="stylesheet" href="{{ asset('assets/css/MarkerCluster.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/MarkerCluster.Default.css') }}">

    <!-- Fullscreen -->
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
        rel='stylesheet' />

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased h-screen">
    <div class="max-w-5xl mx-auto mt-4">
        <header>
            <nav class="border-gray-200 px-2 lg:px-6 py-2.5 lg:mx-0 mx-2">
                <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('assets/images/logo.png') }}" class="mr-3 h-8 sm:h-10" alt="Logo" />
                    </a>
                    <div class="flex items-center lg:order-2">
                        @if (auth()->check())
                            <div class="flex md:order-2">
                                <button id="dropdownAvatarNameButton" data-dropdown-toggle="dropdownAvatarName"
                                    class="flex items-center text-sm font-medium text-gray-900 hover:text-purple-600 dark:hover:text-purple-500 md:mr-0"
                                    type="button">
                                    <span class="sr-only">Open user menu</span>
                                    {{ auth()->user()->name }}
                                    <svg class="w-4 h-4 mx-1.5" aria-hidden="true" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div id="dropdownAvatarName"
                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
                                    style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(1336.67px, 114.167px, 0px);"
                                    data-popper-placement="bottom">
                                    <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium ">
                                            {{ auth()->user()->role }}
                                        </div>
                                        <div class="truncate">
                                            {{ auth()->user()->email }}
                                        </div>
                                    </div>
                                    <div class="py-2">
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="flex w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <i class="fas fa-tachometer-alt mr-2"></i>
                                            <span>Dashboard</span>
                                        </a>
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            <span>Keluar</span>
                                        </button>
                                        </form>
                                    </div>
                                </div>
                                <button data-collapse-toggle="navbar-cta" type="button"
                                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                                    aria-controls="navbar-cta" aria-expanded="false">
                                    <span class="sr-only">Open main menu</span>
                                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <x-link-button route="{{ route('login') }}"
                                class="hidden lg:inline-block text-sm bg-primary">
                                Login
                            </x-link-button>
                        @endif
                        <button data-collapse-toggle="mobile-menu-2" type="button"
                            class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                            aria-controls="mobile-menu-2" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1"
                        id="mobile-menu-2">
                        <ul class="flex flex-col mt-4 lg:flex-row lg:space-x-8 lg:mt-0">
                            <li>
                                <a href="/"
                                    class="block py-2 pr-4 pl-3 {{ request()->routeIs('user.index') ? 'text-primary font-medium' : 'text-gray-700' }} rounded bg-primary-700 lg:bg-transparent lg:text-primary-700 lg:p-0 text-sm">Home</a>
                            </li>
                            <li>
                                <a href="{{ route('user.vector') }}"
                                    class="block py-2 pr-4 pl-3 {{ request()->routeIs('user.vector') ? 'text-primary font-medium' : 'text-gray-700' }} border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  text-sm">Vector</a>
                            </li>
                            <li>
                                <a href="{{ route('user.larvae') }}"
                                    class="block py-2 pr-4 pl-3 {{ request()->routeIs('user.larvae') ? 'text-primary font-medium' : 'text-gray-700' }} border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  text-sm">Larvae</a>
                            </li>
                            <li>
                                <a href="{{ route('user.ksh') }}"
                                    class="block py-2 pr-4 pl-3 {{ request()->routeIs('user.ksh') ? 'text-primary font-medium' : 'text-gray-700' }} border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  text-sm">KSH</a>
                            </li>
                            <li>
                                <a href="{{ route('user.clustering.index') }}"
                                    class="block py-2 pr-4 pl-3 {{ request()->routeIs('user.clustering.index') ? 'text-primary font-medium' : 'text-gray-700' }} border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  text-sm">Cluster</a>
                            </li>
                            <li class="lg:hidden">
                                @if (auth()->check())
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 text-sm">Logout</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 text-sm">Login</a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        {{ $slot }}

        <footer class="sm:flex sm:items-center sm:justify-center p-4 sm:p-6 xl:p-8 ">
            <p class="mb-4 text-sm font-light text-center text-gray-500 dark:text-gray-400 sm:mb-0">
                &copy; 2023 <a href="/" class="hover:underline" target="_blank">Sivemo.com</a>.
                All
                rights reserved.
            </p>
        </footer>
    </div>

    <!-- Jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>

    <!-- Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Marker Cluster -->
    <script src="{{ asset('assets/js/leaflet.markercluster.js') }}"></script>

    <!-- Fullscreen -->
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Chart Js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>

    <script>
        Chart.defaults.font.family = 'Lexend Deca, Outfit, Plus Jakarta Sans, sans-serif';
        Chart.defaults.scale.grid.display = false;
        Chart.defaults.scale.ticks.beginAtZero = true;
        Chart.defaults.scale.ticks.precision = 0;

        $('select').select2();
    </script>

    @stack('js-internal')
</body>

</html>
