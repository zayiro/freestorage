@php
    $companyId = auth()->check() ? auth()->user()->company_id : null;
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Freestorage') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light fixed-top bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Freestorage') }}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                @if(auth()->check())
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                @php
                                    $cart = session()->get('cart', []);
                                    $total = array_sum(array_map(fn($item) => $item['sales_price'] * $item['quantity'], $cart));
                                    $total_price = number_format($total, 2);
                                @endphp
                                <a id="cart_menu" href="{{ route('cart.show') }}" class="btn btn-outline-primary position-relative" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $total_price }} en el carrito">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span class="position-absolute top-1 mt-1 start-100 translate-middle badge rounded-pill bg-danger" id="cart_count_menu">                                        
                                        {{ count($cart) }}
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                       Tienda
                                    </a>
                                    <a class="dropdown-item" href="{{ route('scanner') }}">
                                        <i class="fas fa-barcode"></i>
                                        Ir al escáner
                                    </a>
                                    <a class="dropdown-item" href="{{ route('administration.index') }}">Administración</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 pt-5">
            @yield('content')    
            
            <!-- Contenedor para los Toasts -->
            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>
        </main>

        <footer class="container py-5">
            <div class="d-flex flex-column flex-sm-row justify-content-between py-4 my-4 border-top"> 
                <p>© 2026 Zayiro, Inc. All rights reserved.</p> 
                <ul class="list-unstyled d-flex"> 
                    <li class="ms-3">
                        <a class="link-body-emphasis" href="#" aria-label="Instagram">
                            <svg class="bi" width="24" height="24"><use xlink:href="#instagram"></use></svg>
                        </a>
                    </li> 
                    <li class="ms-3">
                        <a class="link-body-emphasis" href="#" aria-label="Facebook">
                            <svg class="bi" width="24" height="24" aria-hidden="true"><use xlink:href="#facebook"></use></svg>
                        </a>
                    </li> 
                </ul> 
            </div>
        </footer> 
    </div>
    
    @yield('scripts')
</body>
</html>
