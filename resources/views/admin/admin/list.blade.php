<x-admin.header />
<x-admin.aside />
<x-admin.navbar />
@php
    $locale = app()->getLocale(); // Get the current locale, e.g., 'ar' or 'en'
    $isRtl = $locale === 'ar';
@endphp
<main id="main">
    <div class="container">
        <div class="row pt-4">
            <div class="pagetitle mb-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h1 class="mb-1">{{ __('admin_list.title') }}</h1>
                        <ol class="breadcrumb d-flex {{ $isRtl ? 'text-end' : 'text-start' }}"
                            dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('admin_list.breadcrumb_main') }}</a>
                            </li>
                            <li class="mx-2">-</li>
                            <li class="breadcrumb-item active">
                                {{ __('admin_list.breadcrumb_active') }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- End Breadcrumbs with a page title -->

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <x-data-table :headers="$headers" :rows="$rows" :url="$url" />
                    </div>
                </div>
            </div>

            <!-- Pagination links -->
            <div class="mt-4">
                {{ $admins->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</main>
<style>
    .pagination .page-link {
        border-radius: 10px;
    }

    .pagination .page-link svg {
        width: 14px !important;
        height: 14px !important;
    }
</style>

<x-admin.footer />
