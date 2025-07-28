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
                @if (session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
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

                    <div class="card mb-3">
                    <div class="card-body">
    <div class="d-flex flex-column flex-md-row align-items-start gap-3">
        @if (!empty($product->productImages->first()?->images))
            <img src="{{ asset('storage/' . $product->productImages->first()->images) }}"
                 alt=""
                 class="img-fluid"
                 style="max-width: 100px;">
        @endif

        <div class="flex-grow-1 w-100">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                <h5 class="card-title text-truncate mb-1 fc-black">{{ $product->name ?? 'Product' }}</h5>
                <h5 class="fw-bolder text-truncate fc-black mb-1">LE {{ number_format($priceTotal, 2) }}</h5>
            </div>

            <div class="d-flex flex-column pt-1">
                <p class="text-truncate mb-0 fc-gray">{{ __('cart.color') }}: {{ $product->color ?? '-' }}</p>
                <p class="text-truncate mb-0 fc-gray">{{ __('cart.size') }}: {{ $size }}</p>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pt-2">
                <div class="d-flex align-items-center gap-2 mb-2 mb-sm-0">
                    <span class="quantity fw-bolder fs-5">{{ $quantity }}</span>
                </div>
                <div class="d-flex align-items-center">
                    @if (!$isGuest)
                        <a href="{{ route('cart.delete', ['id' => $item->id]) }}">
                            <i class="fa-regular fa-trash-can pointer fs-4" title="{{ __('cart.delete') }}"></i>
                        </a>
                    @else
                        <a href="{{ route('cart.guest.delete', ['key' => $item->key]) }}">
                            <i class="fa-regular fa-trash-can pointer fs-4" title="{{ __('cart.delete') }}"></i>
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
                        <h5 class="fw-bolder fs-3">{{ __('cart.summary') }}</h5>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fw-bolder fc-black pb-2">{{ __('cart.your_order') }}</h5>
                                <div class="d-flex justify-content-between align-items-center pb-3">
                                    <p class="mb-0">{{ __('cart.subtotal') }}</p>
                                    <p class="fw-bolder text-truncate fc-black mb-0">LE {{ number_format($subtotal, 2) }}</p>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bolder text-truncate fc-black mb-0">{{ __('cart.total') }}</h5>
                                    <h5 class="fw-bolder text-truncate fc-black mb-0">LE {{ number_format($total, 2) }}</h5>
                                </div>

                                <a href="{{ route('checkout.index') }}" class="solidBtn w-100 mt-3 py-2 gap-3">
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
