
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $id ? $categoryName->name : __('web.all_products') }} | Hayah Fashion</title>
    <meta name="description" content="{{ __('web.description') }}">
    
    <meta property="og:title" content="Hayah Fashion - {{ $id ? $categoryName->name : 'Shop' }}">
    <meta property="og:image" content="{{ asset('assets/img/logo.png') }}">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css">

    <style>
        :root { --brand-black: #111111; --soft-bg: #f8f9fa; }
        body { font-family: 'Inter', sans-serif; background-color: #fff; color: var(--brand-black); }

        /* Navbar & Nav Fixes */
        .navbar-custom { background: #fff; border-bottom: 1px solid #eee; sticky: top; z-index: 1000; }
        .nav-link { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: var(--brand-black) !important; }

        /* Sidebar Styling */
        .card-aside { border: none; background: var(--soft-bg); border-radius: 15px; padding: 20px; }
        .filter-title { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .nav-pills .nav-link { color: #555; transition: 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .nav-pills .nav-link.active, .nav-pills .nav-link:hover { background-color: #000 !important; color: #fff !important; }

        /* Product Card Fixes */
        .product-wrapper { position: relative; overflow: hidden; border-radius: 12px; background: #f4f4f4; }
        .list-product-img { width: 100%; aspect-ratio: 4 / 5; object-fit: cover; transition: transform 0.8s ease; }
        .product-card:hover .list-product-img { transform: scale(1.06); }
        
        .btn-cart-list { 
            position: absolute; bottom: 15px; right: 15px; background: #fff; border: none; width: 45px; height: 45px; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); opacity: 0; transform: translateY(10px); transition: 0.3s ease; 
        }
        .product-card:hover .btn-cart-list { opacity: 1; transform: translateY(0); }
        .btn-cart-list:hover { background: #000; color: #fff; }

        /* Pagination Fix */
        .pagination { margin-bottom: 0; }
        .pagination svg { width: 20px !important; height: 20px !important; display: inline-block !important; }
        .page-link { color: #000; border: none; margin: 0 3px; border-radius: 5px !important; }
        .page-item.active .page-link { background-color: #000; border-color: #000; }

        /* Footer Inversion */
        .social-icon img { filter: invert(1); opacity: 0.7; transition: 0.3s; }
        .social-icon:hover img { opacity: 1; }
        @media (max-width: 991px) {
    #filterDropdown .dropdown-menu {
        min-width: 180px !important;
    }
    
    #filterDropdown .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
    }
}
    </style>
</head>
<body>

<x-web.navbar />

<section class="pb-5">
    <div class="container py-5">
        <div class="row">
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card card-aside position-sticky" style="top: 100px;">
                    <h6 class="filter-title">{{ __('web.filter') }}</h6>
                    <nav class="nav flex-column nav-pills">
                        <a href="{{ route('product.List') }}" class="nav-link {{ !$id ? 'active' : '' }}">
                            {{ __('web.all_products') }}
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ url( '/product/list/category/' . $category->id) }}"
                               class="nav-link {{ $id == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            <main class="col-lg-9 col-12">
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap">
                    <h1 class="fw-bold h2 mb-0">{{ $id ? $categoryName->name : __('web.all_products') }}</h1>

                    <div class="dropdown d-lg-none" id="filterDropdown">
                        <button id="filterToggle" class="btn btn-dark rounded-pill px-4" type="button" aria-expanded="false">
                            <i class="fa-solid fa-sliders me-2"></i> {{ __('web.filter') }}
                        </button>
                        <ul id="filterMenu" class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('product.List') }}">{{ __('web.all_products') }}</a></li>
                            @foreach ($categories as $category)
                                <li><a class="dropdown-item" href="{{ url('/product/list/category/' . $category->id) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                    @foreach ($data as $product)
                        <div class="col-6 col-md-4 mb-4">
                            <div class="product-card">
                                <div class="product-wrapper">
                                    <a href="{{ route('product.show', [$product->id]) }}">
                                        <img src="{{ $product->productImages->isNotEmpty() ? asset('storage/' . $product->productImages->first()->images) : asset('assets/img/default.jpg') }}"
                                             class="list-product-img" alt="{{ $product->name }}">
                                    </a>
                                    
                               
                                </div>

                                <div class="pt-3">
                                    <h2 class="h6 fw-bold mb-1">{{ $product->name }}</h2>
                                    <div class="price-box">
                                        @if ($product->sale)
                                            <span class="text-danger fw-bold">{{ number_format($product->price - ($product->price * $product->sale / 100), 2) }} LE</span>
                                            <span class="text-muted text-decoration-line-through small ms-1">{{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="fw-bold">{{ number_format($product->price, 2) }} LE</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {!! $data->links('pagination::bootstrap-5') !!}
                </div>
            </main>
        </div>
    </div>
</section>

<x-web.footer />

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('filterToggle');
        const menu = document.getElementById('filterMenu');
        const container = document.getElementById('filterDropdown');

        if (!toggle || !menu || !container) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('show');
            menu.classList.toggle('show', !isOpen);
            container.classList.toggle('show', !isOpen);
            toggle.setAttribute('aria-expanded', (!isOpen).toString());
        });

        document.addEventListener('click', function (e) {
            if (!container.contains(e.target)) {
                menu.classList.remove('show');
                container.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>

</body>
</html>
