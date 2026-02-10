<style>
    /* Global Refinements */
    :root { --brand-black: #111111; --soft-gray: #f9f9f9; }
    body { background-color: #fff; }

    /* Modern Hero Section */
    .hero {
        background-size: cover;
        background-position: center;
        min-height: 600px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        border-radius: 20px;
    }
    
    .hero-content {
        background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, transparent 100%);
        padding: 60px;
        border-radius: 20px;
    }

    /* Product Card - Boutique Style */
    .product-card {
        border: none;
        background: none;
        transition: all 0.4s ease;
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        background-color: var(--soft-gray);
    }

    .product-img {
        transition: transform 1.2s cubic-bezier(0.19, 1, 0.22, 1);
        width: 100%;
        aspect-ratio: 4 / 5; /* Modern portrait ratio */
        object-fit: cover;
    }

    .product-card:hover .product-img {
        transform: scale(1.08);
    }

    /* Floating Cart Button Fix */
    .btn-cart-overlay {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: #fff;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateY(15px);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: 10;
    }

    .product-card:hover .btn-cart-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .btn-cart-overlay:hover {
        background: var(--brand-black);
        color: #fff !important;
    }

    .btn-cart-overlay:hover i {
        color: #fff !important;
    }

    /* Sales Badge */
    .sale-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ff4b2b;
        color: #fff;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
    }

    /* Section Typography */
    .section-title h2 {
        letter-spacing: -1px;
        color: var(--brand-black);
    }

    /* Category Modernization */
    .category-box {
        height: 500px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-end;
        background-size: cover;
        background-position: center;
        transition: transform 0.5s ease;
    }

    .category-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.8) 10%, transparent 100%);
        width: 100%;
        padding: 40px;
        transition: padding 0.4s ease;
    }

    .category-box:hover .category-overlay {
        padding-bottom: 50px;
    }

    /* Custom Arrows */
    .custom-ctrl { opacity: 1; width: 40px; }
    .custom-ctrl-icon {
        background-color: var(--brand-black) !important;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        background-size: 40%;
    }
</style>

<x-web.header />
<x-web.navbar />

<section id="home" class="pt-4 pb-5">
    <div class="container">
        <div class="hero" style="background-image: url('{{ asset('assets/img/main2.jpg') }}');">
            <div class="hero-content text-white">
                <span class="text-uppercase fw-bold mb-2 d-block tracking-widest" style="letter-spacing: 3px; font-size: 0.8rem;">New Season Arrival</span>
                <h1 class="display-2 fw-bold mb-3">{{ __('web.brand_collection_title') }}</h1>
                <p class="lead mb-4 opacity-75 w-75 d-none d-md-block">{{ __('web.brand_collection_desc') }}</p>
                <a href="{{ url('/product/list/category') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg">
                    {{ __('web.discover') }}
                </a>
            </div>
        </div>
    </div>
</section>

<section id="newCollection" class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div class="section-title">
                <h2 class="fw-bold display-5 mb-0">{{ __('web.new_collection') }}</h2>
                <p class="text-muted mt-2">{{ __('web.new_collection_desc') }}</p>
            </div>
            <a href="{{ url( '/product/list/category') }}" class="text-dark fw-bold text-decoration-none border-bottom border-2 border-dark pb-1 d-none d-md-block">
                {{ __('web.show_all') }}
            </a>
        </div>

        <div id="newCollectionCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($products->chunk(4) as $chunkIndex => $chunk)
                    <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                        <div class="row g-3 g-md-4">
                            @foreach($chunk as $product)
                                <div class="col-6 col-lg-3">
                                    <div class="product-card h-100">
                                        <div class="image-wrapper">
                                            @if($product->sale)
                                                <div class="sale-badge">-{{ $product->sale }}%</div>
                                            @endif
                                            
                                            <a href="{{ url('product/show/' . $product->id) }}">
                                                @if ($product->productImages->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $product->productImages->first()->images) }}"
                                                         class="product-img" alt="{{ $product->name }}">
                                                @endif
                                            </a>
                                            
                                            <form action="#" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <button type="submit" class="btn-cart-overlay">
                                                    <i class="fa-solid fa-plus fs-5"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="pt-3">
                                            <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($product->sale)
                                                    <span class="fw-bold text-danger">
                                                        {{ number_format($product->price - ($product->price * $product->sale / 100), 2) }} LE
                                                    </span>
                                                    <span class="text-muted text-decoration-line-through small fw-light">
                                                        {{ number_format($product->price, 2) }}
                                                    </span>
                                                @else
                                                    <span class="fw-bold">{{ number_format($product->price, 2) }} LE</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="carousel-control-prev custom-ctrl" type="button" data-bs-target="#newCollectionCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon custom-ctrl-icon"></span>
            </button>
            <button class="carousel-control-next custom-ctrl" type="button" data-bs-target="#newCollectionCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon custom-ctrl-icon"></span>
            </button>
        </div>
    </div>
</section>

<section id="category" class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-6">
                <div class="category-box" style="background-image: url('{{ asset('assets/img/main.jpg') }}');">
                    <div class="category-overlay">
                        <h3 class="fw-bold text-white display-4 mb-3">{{ __('web.top') }}</h3>
                        <a href="{{ url('product/list/category/1') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold">
                            {{ __('web.see_details') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="category-box" style="background-image: url('{{ asset('assets/img/main3.jpg') }}');">
                    <div class="category-overlay">
                        <h3 class="fw-bold text-white display-4 mb-3">{{ __('web.Long Sleeve') }}</h3>
                        <a href="{{ url('product/list/category/5') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold">
                            {{ __('web.see_details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<x-web.footer />