@php $isRtl = app()->getLocale() === 'ar'; @endphp

<x-admin.header />
<x-admin.aside />
<x-admin.navbar />

<main id="main">
    <div class="container">
        <div class="row pt-4">
            <div class="pagetitle">
                <h1>{{ __('guest.show_title') }}</h1>
                <nav>
                    <ol class="breadcrumb d-flex {{ $isRtl ? 'text-end' : 'text-start' }}"
                        dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                        <li class="breadcrumb-item">
                            <a href="/">{{ __('guest.breadcrumb_main') }}</a>
                        </li>
                        <li class="mx-2">-</li>
                        <li class="breadcrumb-item active">
                            {{ __('guest.breadcrumb_active') }}
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="container">
                <h2>{{ __('guest.addresses') }}</h2>
                <div class="card mb-2">
                    <div class="card-body p-2">
                        @if ($guest)
                            @php
                                $address = $guest->address->first();
                            @endphp

                            <div class="address-section mb-1">
                                <p class="mb-0"><strong>{{ __('guest.address_line1') }}:</strong>
                                    {{ $address->address_line1 ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>{{ __('guest.address_line2') }}:</strong>
                                    {{ $address->address_line2 ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>{{ __('guest.city') }}:</strong> {{ $address->city ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>{{ __('guest.postal_code') }}:</strong>
                                    {{ $address->postal_code ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>{{ __('guest.mobile') }}:</strong>
                                    {{ $address->phone_number ?? 'N/A' }}</p>
                                <hr class="my-1">
                            </div>
                        @else
                            <p><strong>{{ __('guest.address') }}:</strong> {{ __('guest.not_available') }}</p>
                        @endif
                    </div>
                </div>

                <h2>{{ __('guest.orders') }}</h2>
                <div class="mb-3">
                    <input type="text" id="orderSearch" class="form-control"
                        placeholder="{{ __('guest.search_orders') }}" onkeyup="filterOrders()">
                </div>

                <div class="card mb-3">
                    <div class="card-body p-2">
                        @if ($guest)
                            <ul id="orderList" class="list-group list-group-flush">
                                @foreach ($guest->orders as $order)
                                    <li class="list-group-item order-item">
                                        <a href="{{ url('admin/order/edit/' . $order->id) }}">
                                            <strong>{{ __('guest.order') }} #{{ $order->id }}</strong>
                                        </a>
                                        <div class="order-details">
                                            <span><strong>{{ __('guest.total') }}:</strong>
                                                ${{ $order->total_amount ?? 'N/A' }}</span>,
                                            <span><strong>{{ __('guest.status') }}:</strong>
                                                {{ $order->status ?? __('guest.pending') }}</span>,
                                            <span><strong>{{ __('guest.discount') }}:</strong>
                                                {{ $order->discountCode->code ?? 'N/A' }}</span>,
                                            <span><strong>{{ __('guest.city') }}:</strong>
                                                {{ $order->city->name ?? 'N/A' }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p><strong>{{ __('guest.orders') }}:</strong> {{ __('guest.no_orders') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<x-admin.footer />

<script>
    function filterOrders() {
        const searchInput = document.getElementById('orderSearch');
        const filter = searchInput.value.toLowerCase();
        const orders = document.querySelectorAll('.order-item');

        orders.forEach(order => {
            const orderDetails = order.textContent || order.innerText;
            order.style.display = orderDetails.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>