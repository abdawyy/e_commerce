@php
    $locale = app()->getLocale();

  function getProductFromCartItem($item)
{       

    if ($item instanceof \App\Models\ShoppingCart) {
        return $item->product;
    } elseif (is_array($item)) {
        return \App\Models\products::find($item['product_id']);
    } elseif (is_object($item)) {
        return \App\Models\products::find($item->product->id);
    }

    return null;
}

    function getProductItemSize($item) {
        $sizeId = is_array($item) ? $item['size_id'] : ($item->size_id ?? null);
        $productItem = \App\Models\productItems::find($sizeId);
        return $productItem?->size ?? '-';
    }

function getCartQuantity($item) {
    return $item instanceof \App\Models\ShoppingCart
        ? $item->quantity
        : ($item->quantity ?? 1); // ✅ use object syntax
}


    function getCartItemPrice($product, $quantity) {
        $price = $product->price ?? 0;
        $sale = $product->sale ?? 0;
        $discounted = $price - ($price * $sale / 100);
        return $discounted * $quantity;
    }
@endphp

@section('title', __('cart.title'))
@section('meta_description', __('cart.description'))

<x-web.header />
<x-web.navbar />
<x-web.sidebar />

<section id="cart-page" class="pb-5">
    <div class="container pb-5">
        <div class="row pt-4">
            <div class="col-12">
            </div>

            <div class="col-12 col-lg-7">
                @if (count($cartItems) > 0)
                    <h1 class="fw-bolder fs-3">
                        {{ __('cart.your_cart') }}
                        <span class="fc-gray fw-normal fs-4">
                            ({{ count($cartItems) }} {{ __('cart.items') }})
                        </span>
                    </h1>
                @else
                    <h1 class="fw-bolder fs-3">{{ __('cart.empty') }}</h1>
                @endif

                @foreach ($cartItems as $key => $item)
                    @php
                        $isGuest = !($item instanceof \App\Models\ShoppingCart);
                        $product = getProductFromCartItem($item);
                        $quantity = getCartQuantity($item);
                        $priceTotal = getCartItemPrice($product, $quantity);
                        $size = getProductItemSize($item);
                    @endphp
                    <div class="card mb-3 shadow-sm rounded-3 border-0">
                        <div class="card-body p-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="cart-img rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center"
                                     style="width:110px; height:110px; flex-shrink:0;">
                                    @if (!empty($product->productImages->first()?->images))
                                        <img src="{{ asset('storage/' . $product->productImages->first()->images) }}"
                                            alt="{{ $product->name ?? '' }}"
                                            class="img-fluid" style="max-height:100%; max-width:100%;" loading="lazy" decoding="async">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </div>

                                <div class="flex-grow-1 w-100">
                                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                                        <div class="me-2">
                                            <h5 class="card-title mb-1 text-truncate" style="max-width:40ch;">{{ $product->name ?? 'Product' }}</h5>
                                            <div class="text-muted small">
                                                <span>{{ __('cart.color') }}: {{ $product->color ?? '-' }}</span>
                                                &middot;
                                                <span>{{ __('cart.size') }}: {{ $size }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end mt-2 mt-md-0">
                                            <h5 class="fw-bolder mb-1">LE {{ number_format($priceTotal, 2) }}</h5>
                                            <div class="mt-1">
                                                <span class="badge bg-secondary">Qty: {{ $quantity }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            {{-- potential quantity controls go here in future --}}
                                        </div>
                                        <div>
                                            @if (!$isGuest)
                                                <a href="{{ route('cart.delete', ['id' => $item->id]) }}" class="text-danger ms-2" aria-label="{{ __('cart.delete') }}">
                                                    <i class="fa-regular fa-trash-can fs-5"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('cart.guest.delete', ['key' => $item->key]) }}" class="text-danger ms-2" aria-label="{{ __('cart.delete') }}">
                                                    <i class="fa-regular fa-trash-can fs-5"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($total > 0)
                <div class="col-12 col-lg-5 mb-4">
                    <div style="position: sticky; top: 100px;">
                        <h5 class="fw-bolder fs-4 mb-3">{{ __('cart.summary') }}</h5>
                        <div class="card shadow-sm rounded-3 border-0">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold text-muted mb-3">{{ __('cart.your_order') }}</h6>
                                <div class="d-flex justify-content-between align-items-center pb-2">
                                    <p class="mb-0 text-muted small">{{ __('cart.subtotal') }}</p>
                                    <p class="fw-bolder text-truncate mb-0">LE {{ number_format($subtotal, 2) }}</p>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bolder mb-0">{{ __('cart.total') }}</h6>
                                    <h6 class="fw-bolder mb-0">LE {{ number_format($total, 2) }}</h6>
                                </div>

                                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mt-3 py-2">
                                    {{ __('cart.checkout') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<x-web.footer />
