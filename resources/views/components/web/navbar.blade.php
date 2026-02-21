<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('web.title') }}</title>
    <meta name="description" content="{{ __('web.description') }}">
    <meta name="keywords" content="{{ __('web.keywords') }}">
    <meta name="author" content="Hayah Fashion">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="alternate" hreflang="en" href="{{ url('/lang/en') }}">
    <link rel="alternate" hreflang="ar" href="{{ url('/lang/ar') }}">

    <meta property="og:site_name" content="Hayah Fashion">
    <meta property="og:title" content="{{ __('web.title') }}">
    <meta property="og:description" content="{{ __('web.description') }}">
    <meta property="og:image" content="{{ asset('assets/img/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ app()->getLocale() == 'ar' ? 'ar_AR' : 'en_US' }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('web.title') }}">
    <meta name="twitter:description" content="{{ __('web.description') }}">
    <meta name="twitter:image" content="{{ asset('assets/img/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --brand-dark: #000000; --glass-bg: #ffffff; }
        body { font-family: 'Inter', 'Cairo', sans-serif; color: var(--brand-dark); }

        .promo-bar { background: var(--brand-dark); color: #fff; font-size: 0.75rem; text-align: center; padding: 8px 0; text-transform: uppercase; letter-spacing: 1px; }

        /* Navbar & Dropdown Fixes */
        .navbar-custom { background: var(--glass-bg); border-bottom: 1px solid #eee;  }
        
        .nav-link { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: var(--brand-dark) !important; cursor: pointer; }

        /* This CSS ensures the categories dropdown ALWAYS opens */
        @media (min-width: 992px) {
            .nav-item.dropdown:hover .dropdown-menu {
                display: block !important;
                margin-top: 0;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 8px;
            z-index: 9999 !important;
        }

        .dropdown-item { padding: 10px 20px; font-size: 0.9rem; font-weight: 500; }
        .dropdown-item:hover { background-color: #f8f9fa; color: #000; }

        /* Badge Styling */
        .badge-cart { font-size: 0.65rem; background: #000; color: #fff; border-radius: 50px; padding: 2px 6px; }

        /* Modal Overlay Fix */
        .modal-search-overlay { background: rgba(0,0,0,0.95); }
        .search-input-full { background: transparent; border: none; border-bottom: 2px solid #fff; border-radius: 0; color: #fff; font-size: 2.5rem; text-align: center; width: 100%; }
        .search-input-full:focus { box-shadow: none; border-bottom-color: #fff; color: #fff; }

        [dir="rtl"] .dropdown-menu { text-align: right; }
    </style>
</head>

<body>

    <div class="promo-bar">
        {{ __('web.title') }} — MODERN FASHION REIMAGINED
    </div>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>

            <a class="navbar-brand" href="/">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Hayah" style="width: 100px; height: auto;">
            </a>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link px-3" href="/">{{ __('web.home') }}</a></li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" role="button">
                            {{ __('web.category') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('product.List') }}"><strong>{{ __('web.all_products') }}</strong></a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach ($categories as $category)
                                <li><a class="dropdown-item" href="{{ route('product.List', ['id' => $category->id]) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="#" class="text-dark" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="fa-solid fa-magnifying-glass fs-5"></i>
                </a>
                <a href="{{ route('cart.index') }}" class="text-dark position-relative">
                    <i class="fa-solid fa-bag-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge-cart cart-count">{{ $cartCount ?? 0 }}</span>
                </a>
                <div class="d-none d-md-flex gap-2 ms-2 small fw-bold">
                    <a href="{{ url('/lang/en') }}" class="text-decoration-none {{ app()->getLocale() == 'en' ? 'text-dark' : 'text-muted' }}">EN</a>
                    <a href="{{ url('/lang/ar') }}" class="text-decoration-none {{ app()->getLocale() == 'ar' ? 'text-dark' : 'text-muted' }}">AR</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="offcanvas {{ app()->getLocale() == 'ar' ? 'offcanvas-end' : 'offcanvas-start' }}" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">MENU</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column gap-3">
                <li class="nav-item border-bottom pb-2"><a class="nav-link p-0 fs-5" href="/">{{ __('web.home') }}</a></li>
                <li class="nav-item">
                    <p class="text-muted small mb-2 fw-bold text-uppercase">{{ __('web.category') }}</p>
                    <div class="list-group list-group-flush ps-2">
                        <a href="{{ route('product.List') }}" class="list-group-item list-group-item-action border-0">{{ __('web.all_products') }}</a>
                        @foreach ($categories as $category)
                            <a href="{{ route('product.List', ['id' => $category->id]) }}" class="list-group-item list-group-item-action border-0">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <!-- Language Switcher -->
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url('/lang/en') }}" class="text-decoration-none fw-bold {{ app()->getLocale() == 'en' ? 'text-dark' : 'text-muted' }}">EN</a>
                    <span class="text-muted">|</span>
                    <a href="{{ url('/lang/ar') }}" class="text-decoration-none fw-bold {{ app()->getLocale() == 'ar' ? 'text-dark' : 'text-muted' }}">AR</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-search-overlay" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0"><button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button></div>
                <div class="modal-body d-flex align-items-center">
                    <form method="POST" action="{{ route('product.List') }}" class="container text-center">
                        @csrf
                        <input type="text" name="search" class="search-input-full" placeholder="{{ __('web.search_placeholder') }}" required autofocus>
                        <button type="submit" class="btn btn-outline-light mt-5 px-5 py-3 rounded-pill fw-bold text-uppercase">{{ __('web.search') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>