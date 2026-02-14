<footer class="bg-black text-light border-top border-secondary">
    <div class="container py-5">
        <div class="row gy-4 align-items-center">
            
            <div class="col-12 col-md-4 text-center text-md-start">
                <h5 class="fw-bold letter-spacing-2 mb-2">HAYAH</h5>
                <p class="mb-0 small text-white-50">
                    {!! __('web.footer_rights') !!}
                </p>
            </div>

            <div class="col-12 col-md-4 text-center">
                <div class="d-flex justify-content-center gap-4">
                    <a href="https://www.instagram.com/hayah.homewear" target="_blank" class="social-link">
                        <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram">
                    </a>
                    <a href="https://www.facebook.com/share/..." target="_blank" class="social-link">
                        <img src="https://cdn-icons-png.flaticon.com/512/145/145802.png" alt="Facebook">
                    </a>
                </div>
            </div>

            {{-- <div class="col-12 col-md-4 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end gap-3 small">
                    <a href="/contact" class="footer-link">{{ __('web.cookies') }}</a>
                    <span class="text-white-50">|</span>
                    <a href="/privacy" class="footer-link">{{ __('web.privacy_title') }}</a>
                </div>
            </div> --}}

        </div>
    </div>
</footer>

<style>
    /* High-End Design Touches */
    .letter-spacing-2 { letter-spacing: 2px; }
    
    .social-link img {
        width: 24px;
        height: 24px;
        filter: invert(1); /* Makes black icons pure white */
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .social-link:hover img {
        opacity: 1;
        transform: translateY(-3px);
    }

    .footer-link {
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        transition: color 0.3s ease;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .footer-link:hover {
        color: #fff;
    }

    /* Global Toastr Styling */
    #toast-container > .toast {
        background-color: #111 !important;
        color: #fff !important;
        opacity: 1 !important;
        box-shadow: 0 10px 24px rgba(0,0,0,0.25) !important;
        border-radius: 10px !important;
        background-image: none !important;
    }

    #toast-container > .toast-success {
        background-color: #1f9d55 !important;
        background-image: none !important;
    }

    #toast-container > .toast-error {
        background-color: #dc3545 !important;
        background-image: none !important;
    }

    #toast-container > .toast-warning {
        background-color: #f59e0b !important;
        background-image: none !important;
    }

    #toast-container > .toast-info {
        background-color: #0ea5e9 !important;
        background-image: none !important;
    }

    #toast-container > .toast .toast-message {
        color: #fff !important;
    }

    #toast-container > .toast .toast-close-button {
        color: #fff !important;
        opacity: 0.8 !important;
    }

</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script>
    $(document).ready(function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            positionClass: "toast-top-center",
            timeOut: 4000
        };
        window.__flashShown = window.__flashShown || {};
        @if(session('success'))
            if (!window.__flashShown.success) {
                toastr.success("{{ session('success') }}");
                window.__flashShown.success = true;
            }
        @endif

        @if(session('error'))
            if (!window.__flashShown.error) {
                toastr.error("{{ session('error') }}");
                window.__flashShown.error = true;
            }
        @endif

        @if($errors->any())
            @foreach ($errors->all() as $error)
                if (!window.__flashShown['err_{{ md5($error) }}']) {
                    toastr.error("{{ addslashes($error) }}");
                    window.__flashShown['err_{{ md5($error) }}'] = true;
                }
            @endforeach
        @endif
    });
</script>