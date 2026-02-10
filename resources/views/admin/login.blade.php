<x-admin.header />
<x-admin.navbar />

<div class="admin-auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card admin-auth-card">
                    <div class="row g-0">
                        <div class="col-12 col-md-5 d-none d-md-flex">
                            <div class="admin-auth-visual w-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="mb-3">{{ __('admin.login_title') }}</h2>
                                    <p class="mb-0 text-white-50">{{ __('admin.email') }} • {{ __('admin.password') }}</p>
                                </div>
                                <div class="small text-white-50">HAYAH Admin Portal</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <form action="{{ route('admin.login') }}" method="POST" class="admin-auth-form">
                                <x-alert-error />

                                <div class="d-flex justify-content-center justify-content-md-end gap-2 mb-3 small">
                                    <a class="text-decoration-none {{ app()->getLocale() == 'en' ? 'fw-bold text-primary' : 'text-black' }}" href="{{ url('/lang/en') }}">EN</a>
                                    <span class="text-muted">|</span>
                                    <a class="text-decoration-none {{ app()->getLocale() == 'ar' ? 'fw-bold text-primary' : 'text-black' }}" href="{{ url('/lang/ar') }}">العربية</a>
                                </div>

                                @csrf
                                <h3 class="text-center mb-4 d-md-none">{{ __('admin.login_title') }}</h3>

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('admin.email') }}</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('admin.password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    @error('password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-dark w-100">{{ __('admin.login_button') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-admin.footer />
