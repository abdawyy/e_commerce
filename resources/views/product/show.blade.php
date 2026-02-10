<x-web.header />
<x-web.navbar />
<x-web.sidebar />

<style>
    :root { --brand-black: #111; --brand-green: #28a745; }
    .pointer { cursor: pointer; }
    
    /* Image Gallery Fixes */
    .img-main-container { border-radius: 8px; overflow: hidden; background: #f8f9fa; }
    .img-oneProduct { width: 100%; object-fit: cover; }
    
    /* Fix: Thumbnails Row - prevents cutting off at bottom */
    .thumb-scroll-container { 
        display: flex; 
        gap: 10px; 
        padding: 15px 0; /* Extra vertical padding so thumbnails aren't clipped */
        overflow-x: auto; 
    }
    .img-oneProduct-sub { 
        width: 80px; 
        height: 80px; 
        border-radius: 4px; 
        object-fit: cover; 
        border: 2px solid transparent; 
        transition: 0.3s;
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
    }

    .selector-label {
        border: 1px solid #dee2e6;
        padding: 8px 20px;
        border-radius: 6px;
        transition: 0.2s;
        cursor: pointer;
        font-weight: 500;
    }
    .btn-check:checked + .selector-label {
        background-color: var(--brand-black);
        color: white;
        border-color: var(--brand-black);
    }
    .btn-check:disabled + .selector-label { opacity: 0.3; text-decoration: line-through; }

    /* Review Form Box Fix */
    .review-form-box {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }
</style>

<section id="oneProduct" class="pb-5 mt-4">
    <div class="container">
        <div class="row g-5">
            <div class="col-12 col-lg-6">
                <div class="img-main-container shadow-sm mb-2">
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

            <div class="col-12 col-lg-6">
                <div class="mb-2">
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
                    <p class="text-muted small fw-bold text-uppercase mb-1">Description:</p>
                    <p class="lh-base text-secondary">{{ $product->description }}</p>
                </div>

                <div class="mb-4">
                    <p class="small fw-bold text-uppercase mb-2">Size:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($product->productItems as $productItem)
                            <input type="radio" class="btn-check" name="productItem_id" id="size_{{ $productItem->id }}"
                                   value="{{ $productItem->id }}" {{ $productItem->quantity <= 0 ? 'disabled' : '' }}>
                            <label class="selector-label" for="size_{{ $productItem->id }}">{{ $productItem->size }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <p class="small fw-bold text-uppercase mb-2">Quantity:</p>
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(-1)">-</button>
                        <input type="text" id="quantity" class="form-control text-center fw-bold" value="1" readonly>
                        <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>

                <button class="btn btn-dark w-100 py-3 fw-bold rounded-3" onclick="addToCart({{ $product->id }})">
                    <i class="bi bi-bag-plus-fill me-2"></i> ADD TO CART
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

                    <div id="reviewsList" style="max-height: 400px; overflow-y: auto;">
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
        if (!selectedSize) { alert("Please select a size first"); return; }

        fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify({
                product_id: productId,
                size_id: selectedSize,
                quantity: $('#quantity').val()
            })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                $('.cart-Notify').text(data.cartCount);
                Toastify({ text: data.message, backgroundColor: "green" }).showToast();
            } else if(data.redirect) {
                window.location.href = data.redirect;
            }
        });
    }

    /* Submit Review AJAX */
    $('#ajaxSubmitReview').on('click', function() {
        let rating = $('#ratingInput').val();
        if(rating == 0) return alert("Please select a star rating");

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