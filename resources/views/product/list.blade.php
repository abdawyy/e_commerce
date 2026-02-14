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

        .navbar-custom { background: #fff; border-bottom: 1px solid #eee; z-index: 1000; }
        .nav-link { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: var(--brand-black) !important; }

        /* Sidebar Styling */
        .card-aside { 
            border: 1px solid #dee2e6; 
            background: #fff; 
            border-radius: 8px; 
            padding: 0;
            overflow: hidden;
        }

        .filter-sidebar-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 15px;
        }

        .filter-title { 
            font-size: 0.9rem; 
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .filter-form {
            padding: 35px;
        }

        .filter-section {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .filter-section:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .filter-section-title {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .filter-section input[type="number"],
        .filter-section .form-check-input {
            border: 1px solid #dee2e6;
            cursor: pointer;
        }

        .filter-section input[type="number"] {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 4px;
        }

        .filter-section input[type="number"]:focus {
            border-color: #80bdff;
            box-shadow: none;
        }

        .filter-section .form-check {
            padding: 6px 0;
        }

        .filter-section .form-check-label {
            color: #555;
            font-weight: 500;
            cursor: pointer;
            margin: 0;
            padding-left: 6px;
            font-size: 0.9rem;
        }

        .price-inputs {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .price-inputs input {
            flex: 1;
            padding: 0.5rem 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .price-separator {
            color: #999;
            font-weight: 600;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
        }

        .btn-filter {
            flex: 1;
            padding: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn-filter.apply {
            background: #007bff;
            color: white;
        }

        .btn-filter.apply:hover {
            background: #0056b3;
        }

        .btn-filter.clear {
            background: #e9ecef;
            color: #333;
            border: 1px solid #dee2e6;
        }

        .btn-filter.clear:hover {
            background: #dee2e6;
        }

        .nav-pills .nav-link { 
            color: #555; 
            border-radius: 4px; 
            margin-bottom: 5px; 
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        .nav-pills .nav-link.active, .nav-pills .nav-link:hover { 
            background-color: #000 !important; 
            color: #fff !important;
        }

        /* Product Card Fixes */
        .product-wrapper { position: relative; overflow: hidden; border-radius: 8px; background: #f4f4f4; }
        .list-product-img { width: 100%; aspect-ratio: 4 / 5; object-fit: cover; transition: transform 0.6s ease; }
        .product-card:hover .list-product-img { transform: scale(1.05); }
        
        .btn-cart-list { 
            position: absolute; bottom: 15px; right: 15px; background: #fff; border: none; width: 40px; height: 40px; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); opacity: 0; transform: translateY(10px); transition: 0.3s ease; 
        }
        .product-card:hover .btn-cart-list { opacity: 1; transform: translateY(0); }
        .btn-cart-list:hover { background: #000; color: #fff; }

        /* Pagination Fix */
        .pagination { margin-bottom: 0; }
        .pagination svg { width: 20px !important; height: 20px !important; display: inline-block !important; }
        .page-link { color: #000; border: none; margin: 0 3px; border-radius: 4px !important; }
        .page-item.active .page-link { background-color: #000; border-color: #000; }

        /* Mobile Filter Modal */
        .offcanvas { width: 100% !important; }
        .offcanvas-header { background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .offcanvas-body { padding: 15px; }

        @media (max-width: 991px) {
            .filter-form { padding: 32px; }
            .filter-section { margin-bottom: 12px; padding-bottom: 10px; }
        }
    </style>
</head>
<body>

<x-web.navbar />

<section class="pb-5">
    <div class="container py-5">
        <div class="row">
            <!-- Desktop Sidebar -->
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card card-aside position-sticky" style="top: 100px;">
                    <div class="filter-sidebar-header">
                        <h6 class="filter-title">{{ __('web.filter') }}</h6>
                    </div>
                    
                    <form method="GET" action="{{ route('product.List', ($id ? ['id' => $id] : [])) }}" class="filter-form">
                        <!-- Categories -->
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('web.category') }}</h6>
                            <nav class="nav flex-column nav-pills">
                                <a href="{{ route('product.List') }}" class="nav-link {{ !$id ? 'active' : '' }}">
                                    {{ __('web.all_products') }}
                                </a>
                                @foreach ($categories as $category)
                                    <a href="{{ route('product.List', ['id' => $category->id]) }}" 
                                       class="nav-link {{ $id == $category->id ? 'active' : '' }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        <!-- Price Range -->
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('web.price') }}</h6>
                            <div class="price-inputs">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0">
                                <span class="price-separator">-</span>
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0" max="{{ $maxProductPrice }}">
                            </div>
                        </div>

                        <!-- Type -->
                        @if(is_array($types) && count($types) || (is_object($types) && $types->count()))
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('products.type') }}</h6>
                            @foreach ($types as $type)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_id" value="{{ $type->id }}" 
                                           id="type-{{ $type->id }}" {{ request('type_id') == $type->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-{{ $type->id }}">
                                        {{ $type->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Color -->
                        @if(is_array($colors) && count($colors) || (is_object($colors) && $colors->count()))
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('web.color') }}</h6>
                            @foreach ($colors as $c)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="color" value="{{ $c }}" 
                                           id="color-{{ $c }}" {{ request('color') == $c ? 'checked' : '' }}>
                                    <label class="form-check-label" for="color-{{ $c }}">
                                        {{ $c }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Size -->
                        @if(is_array($sizes) && count($sizes) || (is_object($sizes) && $sizes->count()))
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('web.size') }}</h6>
                            @foreach ($sizes as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size" value="{{ $s }}" 
                                           id="size-{{ $s }}" {{ request('size') == $s ? 'checked' : '' }}>
                                    <label class="form-check-label" for="size-{{ $s }}">
                                        {{ $s }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Stock Status -->
                        <div class="filter-section">
                            <h6 class="filter-section-title">{{ __('web.stock') }}</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stock_status" value="in_stock" 
                                       id="in-stock" {{ request('stock_status') == 'in_stock' ? 'checked' : '' }}>
                                <label class="form-check-label" for="in-stock">
                                    {{ __('web.in_stock') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stock_status" value="out_of_stock" 
                                       id="out-stock" {{ request('stock_status') == 'out_of_stock' ? 'checked' : '' }}>
                                <label class="form-check-label" for="out-stock">
                                    {{ __('web.out_of_stock') }}
                                </label>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn-filter apply">Filter</button>
                            <a href="{{ route('product.List', ($id ? ['id' => $id] : [])) }}" class="btn-filter clear">Clear</a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col-lg-9 col-12">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h1 class="fw-bold h3 mb-0">{{ $id ? $categoryName->name : __('web.all_products') }}</h1>

                    <!-- Mobile Filter Button -->
                    <button class="btn btn-dark btn-sm d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterPanel" aria-controls="mobileFilterPanel">
                        <i class="fas fa-sliders-h me-2"></i>{{ __('web.filter') }}
                    </button>
                </div>

                <!-- Products Grid -->
                <div class="row g-3 g-md-4">
                    @foreach ($data as $product)
                        <div class="col-6 col-md-4 mb-3">
                            <div class="product-card">
                                <div class="product-wrapper">
                                    <a href="{{ route('product.show', [$product->id]) }}">
                                        <img src="{{ $product->productImages->isNotEmpty() ? asset('storage/' . $product->productImages->first()->images) : asset('assets/img/default.jpg') }}"
                                            class="img-fluid list-product-img" alt="{{ $product->name }}" loading="lazy" decoding="async">
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {!! $data->links('pagination::bootstrap-5') !!}
                </div>
            </main>
        </div>
    </div>
</section>

<!-- Mobile Filter Modal (Offcanvas) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileFilterPanel" aria-labelledby="mobileFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileFilterLabel">{{ __('web.filter') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <form method="GET" action="{{ route('product.List', ($id ? ['id' => $id] : [])) }}" class="filter-form">
            <!-- All Filter Sections in Mobile -->
            
            <!-- Categories -->
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('web.category') }}</h6>
                <nav class="nav flex-column nav-pills">
                    <a href="{{ route('product.List') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        {{ __('web.all_products') }}
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('product.List', ['id' => $category->id]) }}" class="nav-link" data-bs-dismiss="offcanvas">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Price Range -->
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('web.price') }}</h6>
                <div class="price-inputs">
                    <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0">
                    <span class="price-separator">-</span>
                    <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0" max="{{ $maxProductPrice }}">
                </div>
            </div>

            <!-- Type -->
            @if(is_array($types) && count($types) || (is_object($types) && $types->count()))
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('products.type') }}</h6>
                @foreach ($types as $type)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type_id" value="{{ $type->id }}" 
                               id="m-type-{{ $type->id }}" {{ request('type_id') == $type->id ? 'checked' : '' }}>
                        <label class="form-check-label" for="m-type-{{ $type->id }}">
                            {{ $type->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Color -->
            @if(is_array($colors) && count($colors) || (is_object($colors) && $colors->count()))
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('web.color') }}</h6>
                @foreach ($colors as $c)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="color" value="{{ $c }}" 
                               id="m-color-{{ $c }}" {{ request('color') == $c ? 'checked' : '' }}>
                        <label class="form-check-label" for="m-color-{{ $c }}">
                            {{ $c }}
                        </label>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Size -->
            @if(is_array($sizes) && count($sizes) || (is_object($sizes) && $sizes->count()))
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('web.size') }}</h6>
                @foreach ($sizes as $s)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="size" value="{{ $s }}" 
                               id="m-size-{{ $s }}" {{ request('size') == $s ? 'checked' : '' }}>
                        <label class="form-check-label" for="m-size-{{ $s }}">
                            {{ $s }}
                        </label>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Stock Status -->
            <div class="filter-section">
                <h6 class="filter-section-title">{{ __('web.stock') }}</h6>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="stock_status" value="in_stock" 
                           id="m-in-stock" {{ request('stock_status') == 'in_stock' ? 'checked' : '' }}>
                    <label class="form-check-label" for="m-in-stock">
                        {{ __('web.in_stock') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="stock_status" value="out_of_stock" 
                           id="m-out-stock" {{ request('stock_status') == 'out_of_stock' ? 'checked' : '' }}>
                    <label class="form-check-label" for="m-out-stock">
                        {{ __('web.out_of_stock') }}
                    </label>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-buttons p-3 border-top">
                <button type="submit" class="btn-filter apply">Filter</button>
                <a href="{{ route('product.List', ($id ? ['id' => $id] : [])) }}" class="btn-filter clear">Clear</a>
            </div>
        </form>
    </div>
</div>

<x-web.footer />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
