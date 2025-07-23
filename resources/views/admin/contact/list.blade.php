<x-admin.header />
<x-admin.aside />
<x-admin.navbar />

<main id="main">
    <div class="container">
        <div class="row pt-4">
            @php $isRtl = app()->getLocale() === 'ar'; @endphp

            <div class="pagetitle">
                <h1>{{ __('messages.title') }}</h1>
                <nav>
                    <ol class="breadcrumb d-flex {{ $isRtl ? 'text-end' : 'text-start' }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                        <li class="breadcrumb-item">
                            <a href="#">{{ __('messages.breadcrumb_main') }}</a>
                        </li>
                        <li class="mx-2">-</li>
                        <li class="breadcrumb-item active">
                            {{ __('messages.breadcrumb_active') }}
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Data Table for Messages -->
            <x-data-table :headers="$headers" :rows="$rows" :url="$url" />
        </div>

        <!-- Pagination links -->
        <div class="mt-4">
            {{ $data->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</main>

<x-admin.footer />

<style>
    .btn-danger {
        display: none;
    }

    svg {
        width: 5px !important;
        height: 5px !important;
    }
</style>
