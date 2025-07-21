<x-web.header />
<x-web.navbar />
<x-web.sidebar />
<div class="container py-5">
    <!-- Terms Section -->
    <h1 class="text-2xl font-semibold mb-4">{{ __('web.terms_title') }}</h1>
    <p class="mb-8">
        {{ __('web.msg_terms') }}
    </p>

    <!-- Privacy Policy Section -->
    <h1 class="text-2xl font-semibold mb-4">{{ __('web.privacy_title') }}</h1>
    <p>
        {{ __('web.msg_privacy') }}
    </p>
</div>



<x-web.footer />
