<x-web.header />
<x-web.navbar />
<x-web.sidebar />

<style>
    :root { --brand-black: #111; --brand-green: #28a745; --brand-muted: #6b7280; }
    .pointer { cursor: pointer; }

    .page-wrap {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 40%);
        padding: 24px 0 60px;
    }
    .content-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        border: 1px solid #eef2f7;
    }
    .section-title {
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--brand-muted);
        font-weight: 700;
    }
    
    /* Image Gallery Fixes */
    .img-main-container { border-radius: 14px; overflow: hidden; background: #f8f9fa; }
    .img-oneProduct { width: 100%; object-fit: cover; aspect-ratio: 4 / 5; }
    .img-main-container img { transition: transform 0.3s ease; }
    .img-main-container:hover img { transform: scale(1.02); }
    
    /* Fix: Thumbnails Row - prevents cutting off at bottom */
    .thumb-scroll-container { 
        display: flex; 
        gap: 10px; 
        padding: 15px 0; /* Extra vertical padding so thumbnails aren't clipped */
        overflow-x: auto; 
    }
    .img-oneProduct-sub { 
        width: 76px; 
        height: 76px; 
        border-radius: 10px; 
        object-fit: cover; 
        border: 2px solid transparent; 
        transition: 0.3s;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .img-selected { border-color: var(--brand-black); }

    /* UI Components */
    .stock-badge {
        border: 1px solid var(--brand-green);
        color: var(--brand-green);
        border-radius: 50px;
        padding: 2px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        background: #f2fff6;
    }

    .selector-label {
        border: 1px solid #e5e7eb;
        padding: 8px 18px;
        border-radius: 10px;
        transition: 0.2s;
        cursor: pointer;
        font-weight: 600;
        background: #fff;
    }
    .btn-check:checked + .selector-label {
        background-color: var(--brand-black);
        color: white;
        border-color: var(--brand-black);
    }
    .btn-check:disabled + .selector-label { opacity: 0.3; text-decoration: line-through; }

    .qty-group .btn { width: 42px; }
    .qty-group .form-control { border-left: 0; border-right: 0; }

    .cta-btn {
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .review-card {
        border-radius: 14px;
        border: 1px solid #eef2f7;
        background: #fff;
    }

    .reviews-list {
        max-height: none;
        overflow: visible;
    }

    @media (max-width: 992px) {
        .thumb-scroll-container {
            flex-wrap: wrap;
            overflow-x: visible;
        }
    }

    /* Review Form Box Fix */
    .review-form-box {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 30px;
    }
</style>

<section id="oneProduct" class="page-wrap">
    <div class="container">
        <div class="row g-5">
            <div class="col-12 col-lg-6">
                <div class="content-card p-3 p-md-4">
                    <div class="img-main-container mb-2">
                        <img id="mainImage" class="img-oneProduct pointer"
                             src="{{ $product->productImages->first() ? asset('storage/' . $product->productImages->first()->images) : asset('assets/img/default.jpg') }}"
                             onclick="openImage(this)">
                    </div>
                    
                    <div class="thumb-scroll-container">
                        @foreach ($product->productImages as $productImage)
                            <img class="img-oneProduct-sub {{ $loop->first ? 'img-selected' : '' }}"
                                 src="{{ asset('storage/' . $productImage->images) }}" 
                                 onclick="changeMainImage(this)">
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="content-card p-4 p-md-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        @php $totalQuantity = $product->productItems->sum('quantity'); @endphp
                        <span class="stock-badge">{{ $totalQuantity > 0 ? 'In Stock' : 'Out of Stock' }}</span>
                    </div>

                    <h1 class="fw-bold fs-3 mb-1">{{ $product->name }}</h1>
                    <h2 class="fw-bold fs-2 mb-4">
                        @if ($product->sale)
                            <span class="text-danger">{{ number_format($product->price - ($product->price * $product->sale / 100), 2) }} LE</span>
                            <span class="text-muted text-decoration-line-through fs-5 fw-normal ms-2">{{ number_format($product->price, 2) }}</span>
                        @else
                            {{ number_format($product->price, 2) }} LE
                        @endif
                    </h2>

                    <div class="mb-4">
                        <p class="section-title mb-1">Description</p>
                        <p class="lh-base text-secondary">{{ $product->description }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="section-title mb-2">Size</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($product->productItems as $productItem)
                                <input type="radio" class="btn-check" name="productItem_id" id="size_{{ $productItem->id }}"
                                       value="{{ $productItem->id }}" {{ $productItem->quantity <= 0 ? 'disabled' : '' }}>
                                <label class="selector-label" for="size_{{ $productItem->id }}">{{ $productItem->size }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="section-title mb-2">Quantity</p>
                        <div class="input-group qty-group" style="width: 160px;">
                            <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(-1)">-</button>
                            <input type="text" id="quantity" class="form-control text-center fw-bold" value="1" readonly>
                            <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>

                    <button class="btn btn-dark w-100 py-3 fw-bold rounded-3 cta-btn" onclick="addToCart({{ $product->id }})">
                        <i class="bi bi-bag-plus-fill me-2"></i> Add to Cart
                    </button>

                    <hr class="my-5">

                    <div id="reviews-section">
                        <h4 class="fw-bold mb-4">Customer Reviews</h4>
                    
                    @auth
                        <div class="review-form-box shadow-sm">
                            <label class="fw-bold small mb-2">Rate this product</label>
                            <div id="submitStars" class="text-muted fs-4 mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star pointer" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" id="ratingInput" value="0">
                            <textarea id="commentInput" class="form-control mb-3" rows="3" placeholder="Write your review..."></textarea>
                            <button class="btn btn-dark px-4" id="ajaxSubmitReview">Submit Review</button>
                        </div>
                    @else
                        <div class="alert alert-light border mb-4 text-center">
                            <p class="mb-0 small text-muted">Please <a href="/login" class="text-dark fw-bold">Login</a> to leave a review.</p>
                        </div>
                    @endauth

                    <div id="reviewsList" class="review-card p-3 reviews-list">
                        @forelse ($product->reviews as $review)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold small text-dark">{{ $review->user->name }}</span>
                                    <div class="text-warning small">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="mb-0 small text-muted mt-1">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted small">No reviews yet. Be the first to rate!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #toast-container > .toast {
        background-color: #111 !important;
        color: #fff !important;
        opacity: 1 !important;
    }

    #toast-container > .toast-success {
        background-color: #1f9d55 !important;
    }

    #toast-container > .toast-error {
        background-color: #dc3545 !important;
    }

    #toast-container > .toast .toast-message {
        color: #fff !important;
    }
</style>

<x-web.footer />

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    /* Image Gallery Logic */
    function changeMainImage(img) {
        document.getElementById('mainImage').src = img.src;
        $('.img-oneProduct-sub').removeClass('img-selected');
        $(img).addClass('img-selected');
    }

    function openImage(img) {
        const overlay = document.createElement('div');
        overlay.style = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); display:flex; align-items:center; justify-content:center; z-index:9999; cursor:zoom-out;";
        const largeImg = document.createElement('img');
        largeImg.src = img.src;
        largeImg.style = "max-width:90%; max-height:90%; border-radius:10px;";
        overlay.appendChild(largeImg);
        document.body.appendChild(overlay);
        overlay.onclick = () => document.body.removeChild(overlay);
    }

    /* Quantity Logic */
    function changeQuantity(val) {
        let qty = parseInt($('#quantity').val());
        if (qty + val >= 1 && qty + val <= 10) $('#quantity').val(qty + val);
    }

    /* Stars Rating Logic */
    $('#submitStars i').on('click', function() {
        let rating = $(this).data('value');
        $('#ratingInput').val(rating);
        $('#submitStars i').removeClass('bi-star-fill text-warning').addClass('bi-star');
        $('#submitStars i').each(function() {
            if ($(this).data('value') <= rating) {
                $(this).removeClass('bi-star').addClass('bi-star-fill text-warning');
            }
        });
    });

    /* Add to Cart AJAX */
    function addToCart(productId) {
        let selectedSize = $('input[name="productItem_id"]:checked').val();
        if (!selectedSize) {
            if (window.toastr) {
                toastr.error("Please select a size first");
            }
            return;
        }

        fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify({
                product_id: productId,
                size_id: selectedSize,
                quantity: parseInt($('#quantity').val(), 10)
            })
        })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || 'Unable to add to cart.');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    $('.cart-Notify, .cart-count').text(data.cartCount);
                    if (window.toastr) {
                        toastr.success(data.message || "Added to cart.");
                    }
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (window.toastr) {
                    toastr.error(data.message || "Unable to add to cart.");
                }
            })
            .catch(err => {
                if (window.toastr) {
                    toastr.error(err.message || "Unable to add to cart.");
                }
            });
    }

    /* Submit Review AJAX */
    $('#ajaxSubmitReview').on('click', function() {
        let rating = $('#ratingInput').val();
        if (rating == 0) {
            if (window.toastr) {
                toastr.error("Please select a star rating");
            }
            return;
        }

        $.ajax({
            url: "/reviews",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_id: "{{ $product->id }}",
                rating: rating,
                comment: $('#commentInput').val()
            },
            success: function() { location.reload(); }
        });
    });
</script>